<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;

class GetScopedPilotProjectNaskahPelaporanUseCase
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService
    ) {
    }

    public function execute(int $id, string $level): PilotProjectNaskahPelaporanReport
    {
        $report = $this->repository->findReport($id);
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->scopeService->authorizeSameLevelAndArea($report, $level, $areaId);
    }
}
