<?php

namespace App\Domains\Wilayah\DataPelatihanKader\UseCases;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;

class GetScopedDataPelatihanKaderUseCase
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function execute(int $id, string $level): DataPelatihanKader
    {
        $dataPelatihanKader = $this->dataPelatihanKaderRepository->find($id);
        $areaId = $this->dataPelatihanKaderScopeService->requireUserAreaId();

        return $this->dataPelatihanKaderScopeService->authorizeSameLevelAndArea($dataPelatihanKader, $level, $areaId);
    }
}





