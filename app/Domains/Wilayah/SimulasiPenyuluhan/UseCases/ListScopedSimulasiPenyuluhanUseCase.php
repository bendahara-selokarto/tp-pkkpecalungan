<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\UseCases;

use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Services\SimulasiPenyuluhanScopeService;

class ListScopedSimulasiPenyuluhanUseCase
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly SimulasiPenyuluhanScopeService $simulasiPenyuluhanScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->simulasiPenyuluhanScopeService->requireUserAreaId();

        return $this->simulasiPenyuluhanRepository->getByLevelAndArea($level, $areaId);
    }
}
