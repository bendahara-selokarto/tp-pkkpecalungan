<?php

namespace App\Domains\Wilayah\WarungPkk\UseCases;

use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedWarungPkkUseCase
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->warungPkkScopeService->requireUserAreaId();

        return $this->warungPkkRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->warungPkkScopeService->requireUserAreaId();

        return $this->warungPkkRepository->getByLevelAndArea($level, $areaId);
    }
}