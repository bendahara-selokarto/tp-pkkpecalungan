<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\UseCases;

use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuNotulenRapatUseCase
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->bukuNotulenRapatScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuNotulenRapatScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuNotulenRapatRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        
        $areaId = $this->bukuNotulenRapatScopeService->requireUserAreaId();
        $creatorIdFilter = $this->bukuNotulenRapatScopeService->resolveCreatorIdFilterForList($level);

        return $this->bukuNotulenRapatRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}


