<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;

class GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService
    ) {
    }

    public function execute(int $id, string $level): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->find($id);
        $areaId = $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->requireUserAreaId();

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->authorizeSameLevelAndArea($dataPemanfaatanTanahPekaranganHatinyaPkk, $level, $areaId);
    }
}



