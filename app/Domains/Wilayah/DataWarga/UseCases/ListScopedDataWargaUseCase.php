<?php

namespace App\Domains\Wilayah\DataWarga\UseCases;

use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataWargaUseCase
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataWargaRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataWargaRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
