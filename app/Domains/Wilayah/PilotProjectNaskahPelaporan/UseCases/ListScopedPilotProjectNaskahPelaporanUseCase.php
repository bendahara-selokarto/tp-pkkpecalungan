<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases;

use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Repositories\PilotProjectNaskahPelaporanRepositoryInterface;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Services\PilotProjectNaskahPelaporanScopeService;
use Illuminate\Support\Collection;

class ListScopedPilotProjectNaskahPelaporanUseCase
{
    public function __construct(
        private readonly PilotProjectNaskahPelaporanRepositoryInterface $repository,
        private readonly PilotProjectNaskahPelaporanScopeService $scopeService
    ) {
    }

    public function execute(string $level): Collection
    {
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->repository->getByLevelAndArea($level, $areaId);
    }
}
