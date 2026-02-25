<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\UseCases;

use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataIndustriRumahTanggaUseCase
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataIndustriRumahTanggaScopeService->requireUserAreaId();

        return $this->dataIndustriRumahTanggaRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataIndustriRumahTanggaScopeService->requireUserAreaId();

        return $this->dataIndustriRumahTanggaRepository->getByLevelAndArea($level, $areaId);
    }
}



