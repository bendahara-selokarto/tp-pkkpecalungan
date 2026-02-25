<?php

namespace App\Domains\Wilayah\DataPelatihanKader\UseCases;

use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataPelatihanKaderUseCase
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataPelatihanKaderScopeService->requireUserAreaId();

        return $this->dataPelatihanKaderRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataPelatihanKaderScopeService->requireUserAreaId();

        return $this->dataPelatihanKaderRepository->getByLevelAndArea($level, $areaId);
    }
}




