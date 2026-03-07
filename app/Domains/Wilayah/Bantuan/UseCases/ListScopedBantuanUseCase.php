<?php

namespace App\Domains\Wilayah\Bantuan\UseCases;

use App\Domains\Wilayah\Bantuan\Repositories\BantuanRepositoryInterface;
use App\Domains\Wilayah\Bantuan\Services\BantuanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBantuanUseCase
{
    public function __construct(
        private readonly BantuanRepositoryInterface $bantuanRepository,
        private readonly BantuanScopeService $bantuanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bantuanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->bantuanScopeService->resolveCreatorIdFilterForList($level);

        return $this->bantuanRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bantuanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->bantuanScopeService->resolveCreatorIdFilterForList($level);

        return $this->bantuanRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}
