<?php

namespace App\Domains\Wilayah\DataWarga\UseCases;

use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataWargaUseCase
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataWargaScopeService->requireUserAreaId();

        return $this->dataWargaRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataWargaScopeService->requireUserAreaId();

        return $this->dataWargaRepository->getByLevelAndArea($level, $areaId);
    }
}
