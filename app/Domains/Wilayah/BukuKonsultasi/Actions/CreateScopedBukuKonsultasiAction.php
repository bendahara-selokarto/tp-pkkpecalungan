<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Actions;

use App\Domains\Wilayah\BukuKonsultasi\DTOs\BukuKonsultasiData;
use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Repositories\BukuKonsultasiRepositoryInterface;
use App\Domains\Wilayah\BukuKonsultasi\Services\BukuKonsultasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBukuKonsultasiAction
{
    public function __construct(
        private readonly BukuKonsultasiRepositoryInterface $bukuKonsultasiRepository,
        private readonly BukuKonsultasiScopeService $bukuKonsultasiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BukuKonsultasi
    {
        $user = auth()->user();
        
        $tahunAnggaran = isset($payload['activity_date']) 
            ? (int) date('Y', strtotime($payload['activity_date']))
            : $this->activeBudgetYearContextService->resolveForUser($user);

        $data = BukuKonsultasiData::fromArray([
            'activity_date' => $payload['activity_date'],
            'description' => $payload['description'],
            'disposition' => $payload['disposition'] ?? null,
            'level' => $level,
            'area_id' => $this->bukuKonsultasiScopeService->requireUserAreaId(),
            'group' => $payload['group'] ?? $this->resolveGroup($user),
            'created_by' => $user->id,
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuKonsultasiRepository->store($data);
    }

    private function resolveGroup($user): string
    {
        $groups = $this->bukuKonsultasiScopeService->resolveGroupsForUser($user);
        return $groups[0] ?? 'sekretaris-tpk';
    }
}
