<?php

namespace App\Domains\Wilayah\TutorKhusus\Actions;

use App\Domains\Wilayah\TutorKhusus\DTOs\TutorKhususData;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Repositories\TutorKhususRepositoryInterface;
use App\Domains\Wilayah\TutorKhusus\Services\TutorKhususScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedTutorKhususAction
{
    public function __construct(
        private readonly TutorKhususRepositoryInterface $tutorKhususRepository,
        private readonly TutorKhususScopeService $tutorKhususScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): TutorKhusus
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        $data = TutorKhususData::fromArray([
            'jenis_tutor' => $payload['jenis_tutor'],
            'jumlah_tutor' => $payload['jumlah_tutor'],
            'keterangan' => $payload['keterangan'] ?? null,
            'tahun_anggaran' => $tahunAnggaran,
            'level' => $level,
            'area_id' => $this->tutorKhususScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->tutorKhususRepository->store($data);
    }
}
