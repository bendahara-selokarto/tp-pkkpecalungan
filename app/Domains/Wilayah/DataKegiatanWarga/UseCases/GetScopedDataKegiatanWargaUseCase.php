<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\UseCases;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Services\DataKegiatanWargaScopeService;

class GetScopedDataKegiatanWargaUseCase
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly DataKegiatanWargaScopeService $dataKegiatanWargaScopeService
    ) {
    }

    public function execute(int $id, string $level): DataKegiatanWarga
    {
        $dataKegiatanWarga = $this->dataKegiatanWargaRepository->find($id);
        $areaId = $this->dataKegiatanWargaScopeService->requireUserAreaId();

        return $this->dataKegiatanWargaScopeService->authorizeSameLevelAndArea($dataKegiatanWarga, $level, $areaId);
    }
}
