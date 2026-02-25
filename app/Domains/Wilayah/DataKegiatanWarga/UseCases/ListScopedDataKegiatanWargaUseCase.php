<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\UseCases;

use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataKegiatanWargaUseCase
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();

        return $this->dataKegiatanWargaRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();

        return $this->dataKegiatanWargaRepository->getByLevelAndArea($level, $areaId);
    }
}
