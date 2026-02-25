<?php

namespace App\Domains\Wilayah\Bkl\UseCases;

use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBklUseCase
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bklScopeService->requireUserAreaId();

        return $this->bklRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bklScopeService->requireUserAreaId();

        return $this->bklRepository->getByLevelAndArea($level, $areaId);
    }
}
