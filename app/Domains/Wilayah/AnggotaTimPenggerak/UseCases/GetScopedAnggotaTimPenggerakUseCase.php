<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\UseCases;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedAnggotaTimPenggerakUseCase
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): AnggotaTimPenggerak
    {
        $anggotaTimPenggerak = $this->anggotaTimPenggerakRepository->find($id);
        $areaId = $this->anggotaTimPenggerakScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->anggotaTimPenggerakScopeService->authorizeSameLevelAreaAndBudgetYear($anggotaTimPenggerak, $level, $areaId, $tahunAnggaran);
    }
}

