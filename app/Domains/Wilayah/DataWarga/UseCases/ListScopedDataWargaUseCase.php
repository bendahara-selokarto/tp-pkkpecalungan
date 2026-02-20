<?php

namespace App\Domains\Wilayah\DataWarga\UseCases;

use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Services\DataWargaScopeService;

class ListScopedDataWargaUseCase
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly DataWargaScopeService $dataWargaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->dataWargaScopeService->requireUserAreaId();

        return $this->dataWargaRepository->getByLevelAndArea($level, $areaId);
    }
}
