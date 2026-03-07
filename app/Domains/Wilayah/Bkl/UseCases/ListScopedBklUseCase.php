<?php

namespace App\Domains\Wilayah\Bkl\UseCases;

use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBklUseCase
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bklScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bklRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bklScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bklRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
