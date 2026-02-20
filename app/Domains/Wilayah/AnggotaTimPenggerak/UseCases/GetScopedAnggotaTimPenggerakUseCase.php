<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\UseCases;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;

class GetScopedAnggotaTimPenggerakUseCase
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function execute(int $id, string $level): AnggotaTimPenggerak
    {
        $anggotaTimPenggerak = $this->anggotaTimPenggerakRepository->find($id);
        $areaId = $this->anggotaTimPenggerakScopeService->requireUserAreaId();

        return $this->anggotaTimPenggerakScopeService->authorizeSameLevelAndArea($anggotaTimPenggerak, $level, $areaId);
    }
}


