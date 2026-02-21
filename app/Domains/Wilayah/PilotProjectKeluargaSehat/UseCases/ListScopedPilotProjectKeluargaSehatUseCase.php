<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use Illuminate\Support\Collection;

class ListScopedPilotProjectKeluargaSehatUseCase
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService
    ) {
    }

    public function execute(string $level): Collection
    {
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->repository->getByLevelAndArea($level, $areaId);
    }
}

