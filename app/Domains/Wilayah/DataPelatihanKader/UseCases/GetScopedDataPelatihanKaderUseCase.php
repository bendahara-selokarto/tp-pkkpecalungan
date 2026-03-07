<?php

namespace App\Domains\Wilayah\DataPelatihanKader\UseCases;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataPelatihanKaderUseCase
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): DataPelatihanKader
    {
        $dataPelatihanKader = $this->dataPelatihanKaderRepository->find($id);
        $areaId = $this->dataPelatihanKaderScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataPelatihanKaderScopeService->authorizeSameLevelAreaAndBudgetYear($dataPelatihanKader, $level, $areaId, $tahunAnggaran);
    }
}




