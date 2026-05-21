<?php

namespace App\Domains\Wilayah\BukuEkspedisi\UseCases;

use App\Domains\Wilayah\BukuEkspedisi\Repositories\BukuEkspedisiRepositoryInterface;
use App\Domains\Wilayah\BukuEkspedisi\Services\BukuEkspedisiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuEkspedisiUseCase
{
    public function __construct(
        private readonly BukuEkspedisiRepositoryInterface $bukuEkspedisiRepository,
        private readonly BukuEkspedisiScopeService $bukuEkspedisiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bukuEkspedisiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->bukuEkspedisiScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuEkspedisiRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bukuEkspedisiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->bukuEkspedisiScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuEkspedisiRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}
