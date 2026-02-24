<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\UseCases;

use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;
use Illuminate\Support\Collection;

class ListScopedLaporanTahunanPkkUseCase
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository,
        private readonly LaporanTahunanPkkScopeService $scopeService
    ) {
    }

    public function execute(string $level): Collection
    {
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->repository->getByLevelAndArea($level, $areaId);
    }
}

