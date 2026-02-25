<?php

namespace App\Domains\Wilayah\Paar\UseCases;

use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedPaarUseCase
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly PaarScopeService $paarScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->paarScopeService->requireUserAreaId();

        return $this->paarRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->paarScopeService->requireUserAreaId();

        return $this->paarRepository->getByLevelAndArea($level, $areaId);
    }
}
