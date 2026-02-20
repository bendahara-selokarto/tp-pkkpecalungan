<?php

namespace App\Domains\Wilayah\Koperasi\UseCases;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;

class GetScopedKoperasiUseCase
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly KoperasiScopeService $koperasiScopeService
    ) {
    }

    public function execute(int $id, string $level): Koperasi
    {
        $koperasi = $this->koperasiRepository->find($id);
        $areaId = $this->koperasiScopeService->requireUserAreaId();

        return $this->koperasiScopeService->authorizeSameLevelAndArea($koperasi, $level, $areaId);
    }
}



