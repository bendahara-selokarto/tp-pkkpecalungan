<?php

namespace App\Domains\Wilayah\DataKeluarga\UseCases;

use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Services\DataKeluargaScopeService;

class ListScopedDataKeluargaUseCase
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly DataKeluargaScopeService $dataKeluargaScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->dataKeluargaScopeService->requireUserAreaId();

        return $this->dataKeluargaRepository->getByLevelAndArea($level, $areaId);
    }
}

