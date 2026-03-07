<?php

namespace App\Domains\Wilayah\Koperasi\UseCases;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedKoperasiUseCase
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly KoperasiScopeService $koperasiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Koperasi
    {
        $koperasi = $this->koperasiRepository->find($id);
        $areaId = $this->koperasiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->koperasiScopeService->authorizeSameLevelAreaAndBudgetYear($koperasi, $level, $areaId, $tahunAnggaran);
    }
}


