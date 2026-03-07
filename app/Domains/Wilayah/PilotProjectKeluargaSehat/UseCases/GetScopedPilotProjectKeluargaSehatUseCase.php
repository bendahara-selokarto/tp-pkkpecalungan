<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPilotProjectKeluargaSehatUseCase
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): PilotProjectKeluargaSehatReport
    {
        $report = $this->repository->findReport($id);
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->scopeService->authorizeSameLevelAreaAndBudgetYear($report, $level, $areaId, $tahunAnggaran);
    }
}
