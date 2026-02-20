<?php

namespace App\Domains\Wilayah\DataKeluarga\UseCases;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;

class GetScopedDataKeluargaUseCase
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService
    ) {
    }

    public function execute(int $id, string $level): DataKeluarga
    {
        $dataKeluarga = $this->dataKeluargaRepository->find($id);
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();

        return $this->dataKeluargaScopeService->authorizeSameLevelAndArea($dataKeluarga, $level, $areaId);
    }
}

