<?php

namespace App\Domains\Wilayah\DataKeluarga\UseCases;

use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedDataKeluargaUseCase
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKeluargaRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataKeluargaRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran);
    }
}
