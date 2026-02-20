<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\UseCases;

use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;

class ListScopedAnggotaTimPenggerakUseCase
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->anggotaTimPenggerakScopeService->requireUserAreaId();

        return $this->anggotaTimPenggerakRepository->getByLevelAndArea($level, $areaId);
    }
}


