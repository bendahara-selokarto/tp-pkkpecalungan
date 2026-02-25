<?php

namespace App\Domains\Wilayah\Inventaris\UseCases;

use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedInventarisUseCase
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->inventarisScopeService->requireUserAreaId();

        return $this->inventarisRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->inventarisScopeService->requireUserAreaId();

        return $this->inventarisRepository->getByLevelAndArea($level, $areaId);
    }
}
