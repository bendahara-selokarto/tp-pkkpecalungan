<?php

namespace App\Domains\Wilayah\Bkr\UseCases;

use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBkrUseCase
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bkrScopeService->requireUserAreaId();

        return $this->bkrRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->bkrScopeService->requireUserAreaId();

        return $this->bkrRepository->getByLevelAndArea($level, $areaId);
    }
}

