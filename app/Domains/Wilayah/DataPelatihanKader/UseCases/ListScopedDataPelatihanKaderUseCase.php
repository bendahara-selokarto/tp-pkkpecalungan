<?php

namespace App\Domains\Wilayah\DataPelatihanKader\UseCases;

use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;

class ListScopedDataPelatihanKaderUseCase
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->dataPelatihanKaderScopeService->requireUserAreaId();

        return $this->dataPelatihanKaderRepository->getByLevelAndArea($level, $areaId);
    }
}





