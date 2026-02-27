<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\UseCases;

use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;

class GetScopedBukuDaftarHadirUseCase
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService
    ) {
    }

    public function execute(int $id, string $level): BukuDaftarHadir
    {
        $item = $this->bukuDaftarHadirRepository->find($id);
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();

        return $this->bukuDaftarHadirScopeService->authorizeSameLevelAndArea($item, $level, $areaId);
    }
}
