<?php

namespace App\Domains\Wilayah\Koperasi\UseCases;

use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Services\KoperasiScopeService;

class ListScopedKoperasiUseCase
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly KoperasiScopeService $koperasiScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->koperasiScopeService->requireUserAreaId();

        return $this->koperasiRepository->getByLevelAndArea($level, $areaId);
    }
}



