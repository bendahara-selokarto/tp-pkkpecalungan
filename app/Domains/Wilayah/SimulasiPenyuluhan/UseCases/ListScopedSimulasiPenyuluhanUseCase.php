<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\UseCases;

use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedSimulasiPenyuluhanUseCase
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->simulasiPenyuluhanScopeService->requireUserAreaId();

        return $this->simulasiPenyuluhanRepository->paginateByLevelAndArea($level, $areaId, $perPage);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->simulasiPenyuluhanScopeService->requireUserAreaId();

        return $this->simulasiPenyuluhanRepository->getByLevelAndArea($level, $areaId);
    }
}