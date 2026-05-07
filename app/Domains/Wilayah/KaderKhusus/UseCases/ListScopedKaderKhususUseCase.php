<?php

namespace App\Domains\Wilayah\KaderKhusus\UseCases;

use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedKaderKhususUseCase
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $actor = $this->kaderKhususScopeService->requireAuthenticatedUser();
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->kaderKhususScopeService->resolveCreatorIdFilterForList($level);

        return $this->kaderKhususRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $actor, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $actor = $this->kaderKhususScopeService->requireAuthenticatedUser();
        $areaId = $this->kaderKhususScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->kaderKhususScopeService->resolveCreatorIdFilterForList($level);

        return $this->kaderKhususRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $actor, $creatorIdFilter);
    }
}
