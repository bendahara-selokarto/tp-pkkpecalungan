<?php

namespace App\Domains\Wilayah\PrestasiLomba\UseCases;

use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedPrestasiLombaUseCase
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        
        $areaId = $this->prestasiLombaScopeService->requireUserAreaId();
        $creatorIdFilter = $this->prestasiLombaScopeService->resolveCreatorIdFilterForList($level);

        return $this->prestasiLombaRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        
        $areaId = $this->prestasiLombaScopeService->requireUserAreaId();
        $creatorIdFilter = $this->prestasiLombaScopeService->resolveCreatorIdFilterForList($level);

        return $this->prestasiLombaRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}


