<?php

namespace App\Domains\Wilayah\BukuKeuangan\UseCases;

use App\Domains\Wilayah\BukuKeuangan\Repositories\BukuKeuanganRepositoryInterface;
use App\Domains\Wilayah\BukuKeuangan\Services\BukuKeuanganScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuKeuanganUseCase
{
    public function __construct(
        private readonly BukuKeuanganRepositoryInterface $bukuKeuanganRepository,
        private readonly BukuKeuanganScopeService $bukuKeuanganScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->bukuKeuanganScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuKeuanganScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuKeuanganRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        
        $areaId = $this->bukuKeuanganScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuKeuanganScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuKeuanganRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}


