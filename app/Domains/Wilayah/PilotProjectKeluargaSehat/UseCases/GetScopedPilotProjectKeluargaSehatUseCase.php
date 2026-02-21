<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;

class GetScopedPilotProjectKeluargaSehatUseCase
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService
    ) {
    }

    public function execute(int $id, string $level): PilotProjectKeluargaSehatReport
    {
        $report = $this->repository->findReport($id);
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->scopeService->authorizeSameLevelAndArea($report, $level, $areaId);
    }
}

