<?php

namespace App\Domains\Wilayah\AnggotaPokja\UseCases;

use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedAnggotaPokjaUseCase
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->anggotaPokjaScopeService->resolveCreatorIdFilterForList($level);

        return $this->anggotaPokjaRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->anggotaPokjaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->anggotaPokjaScopeService->resolveCreatorIdFilterForList($level);

        return $this->anggotaPokjaRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}
