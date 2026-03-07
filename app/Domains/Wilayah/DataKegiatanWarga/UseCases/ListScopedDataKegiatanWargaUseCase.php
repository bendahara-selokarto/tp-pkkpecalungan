<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\UseCases;

use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataKegiatanWargaUseCase
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKegiatanWargaRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKegiatanWargaRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
