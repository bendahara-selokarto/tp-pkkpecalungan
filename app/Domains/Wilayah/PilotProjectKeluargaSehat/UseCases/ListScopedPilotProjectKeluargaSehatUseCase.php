<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedPilotProjectKeluargaSehatUseCase
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->repository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->repository->getByLevelAndArea($level, $areaId);
    }
}
