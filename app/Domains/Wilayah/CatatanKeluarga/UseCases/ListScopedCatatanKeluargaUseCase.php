<?php

namespace App\Domains\Wilayah\CatatanKeluarga\UseCases;

use App\Domains\Wilayah\CatatanKeluarga\Repositories\CatatanKeluargaRepositoryInterface;
use App\Domains\Wilayah\CatatanKeluarga\Services\CatatanKeluargaScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedCatatanKeluargaUseCase
{
    public function __construct(
        private readonly CatatanKeluargaRepositoryInterface $catatanKeluargaRepository,
        private readonly CatatanKeluargaScopeService $catatanKeluargaScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->catatanKeluargaScopeService->requireUserAreaId();

        return $this->catatanKeluargaRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->catatanKeluargaScopeService->requireUserAreaId();

        return $this->catatanKeluargaRepository->getByLevelAndArea($level, $areaId);
    }
}
