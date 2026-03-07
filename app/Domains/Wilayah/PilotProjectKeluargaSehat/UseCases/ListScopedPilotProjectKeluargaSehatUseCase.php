<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases;

use App\Domains\Wilayah\PilotProjectKeluargaSehat\Repositories\PilotProjectKeluargaSehatRepositoryInterface;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Services\PilotProjectKeluargaSehatScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedPilotProjectKeluargaSehatUseCase
{
    public function __construct(
        private readonly PilotProjectKeluargaSehatRepositoryInterface $repository,
        private readonly PilotProjectKeluargaSehatScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->repository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->repository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
