<?php

namespace App\Domains\Wilayah\DataWarga\UseCases;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;

class GetScopedDataWargaUseCase
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function execute(int $id, string $level): DataWarga
    {
        $dataWarga = $this->dataWargaRepository->find($id);
        $areaId = $this->dataWargaScopeService->requireUserAreaId();

        return $this->dataWargaScopeService->authorizeSameLevelAndArea($dataWarga, $level, $areaId);
    }
}
