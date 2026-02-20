<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;

class ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->requireUserAreaId();

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->getByLevelAndArea($level, $areaId);
    }
}



