<?php

namespace App\Domains\Wilayah\Posyandu\UseCases;

use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedPosyanduUseCase
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->posyanduScopeService->requireUserAreaId();

        return $this->posyanduRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->posyanduScopeService->requireUserAreaId();

        return $this->posyanduRepository->getByLevelAndArea($level, $areaId);
    }
}