<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\UseCases;

use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedLaporanTahunanPkkUseCase
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository,
        private readonly LaporanTahunanPkkScopeService $scopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->scopeService->requireUserAreaId();
        $creatorIdFilter = $this->scopeService->resolveCreatorIdFilterForList($level);

        return $this->repository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }
}


