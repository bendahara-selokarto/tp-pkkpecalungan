<?php

namespace App\Domains\Wilayah\Kliping\UseCases;

use App\Domains\Wilayah\Kliping\Repositories\KlipingRepositoryInterface;
use App\Domains\Wilayah\Kliping\Services\KlipingScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedKlipingUseCase
{
    public function __construct(
        private readonly KlipingRepositoryInterface $klipingRepository,
        private readonly KlipingScopeService $klipingScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->klipingScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->klipingScopeService->resolveCreatorIdFilterForList($level);

        return $this->klipingRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->klipingScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->klipingScopeService->resolveCreatorIdFilterForList($level);

        return $this->klipingRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}
