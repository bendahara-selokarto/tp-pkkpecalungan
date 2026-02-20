<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\UseCases;

use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;

class ListScopedDataIndustriRumahTanggaUseCase
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->dataIndustriRumahTanggaScopeService->requireUserAreaId();

        return $this->dataIndustriRumahTanggaRepository->getByLevelAndArea($level, $areaId);
    }
}




