<?php

namespace App\Domains\Wilayah\KejarPaket\UseCases;

use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedKejarPaketUseCase
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly KejarPaketScopeService $kejarPaketScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->kejarPaketScopeService->requireUserAreaId();

        return $this->kejarPaketRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->kejarPaketScopeService->requireUserAreaId();

        return $this->kejarPaketRepository->getByLevelAndArea($level, $areaId);
    }
}