<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\UseCases;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;

class GetScopedDataIndustriRumahTanggaUseCase
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function execute(int $id, string $level): DataIndustriRumahTangga
    {
        $dataIndustriRumahTangga = $this->dataIndustriRumahTanggaRepository->find($id);
        $areaId = $this->dataIndustriRumahTanggaScopeService->requireUserAreaId();

        return $this->dataIndustriRumahTanggaScopeService->authorizeSameLevelAndArea($dataIndustriRumahTangga, $level, $areaId);
    }
}




