<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\UseCases;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Repositories\LaporanTahunanPkkRepositoryInterface;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkScopeService;

class GetScopedLaporanTahunanPkkUseCase
{
    public function __construct(
        private readonly LaporanTahunanPkkRepositoryInterface $repository,
        private readonly LaporanTahunanPkkScopeService $scopeService
    ) {
    }

    public function execute(int $id, string $level): LaporanTahunanPkkReport
    {
        $report = $this->repository->findReport($id);
        $areaId = $this->scopeService->requireUserAreaId();

        return $this->scopeService->authorizeSameLevelAndArea($report, $level, $areaId);
    }
}

