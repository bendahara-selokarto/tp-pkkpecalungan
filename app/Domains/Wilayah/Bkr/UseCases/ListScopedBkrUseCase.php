<?php

namespace App\Domains\Wilayah\Bkr\UseCases;

use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBkrUseCase
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bkrScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bkrRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bkrScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bkrRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
