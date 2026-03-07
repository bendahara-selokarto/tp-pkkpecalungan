<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Actions;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedBukuNotulenRapatAction
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(array $payload, string $level): BukuNotulenRapat
    {
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        $data = BukuNotulenRapatData::fromArray([
            'entry_date' => $payload['entry_date'],
            'title' => $payload['title'],
            'person_name' => $payload['person_name'] ?? null,
            'institution' => $payload['institution'] ?? null,
            'description' => $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $this->bukuNotulenRapatScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'tahun_anggaran' => $tahunAnggaran,
        ]);

        return $this->bukuNotulenRapatRepository->store($data);
    }
}
