<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->find($id);
        $areaId = $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->authorizeSameLevelAreaAndBudgetYear($dataPemanfaatanTanahPekaranganHatinyaPkk, $level, $areaId, $tahunAnggaran);
    }
}
