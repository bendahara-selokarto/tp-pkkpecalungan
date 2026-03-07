<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPilotProjectNaskahPelaporanUseCase
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): PilotProjectNaskahPelaporanReport
    {
        $report = $this->repository->findReport($id);
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->scopeService->authorizeSameLevelAreaAndBudgetYear($report, $level, $areaId, $tahunAnggaran);
    }
}
