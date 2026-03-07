<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\UseCases;

use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\BukuDaftarHadir\Repositories\BukuDaftarHadirRepositoryInterface;
use App\Domains\Wilayah\BukuDaftarHadir\Services\BukuDaftarHadirScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuDaftarHadirUseCase
{
    public function __construct(
        private readonly BukuDaftarHadirRepositoryInterface $bukuDaftarHadirRepository,
        private readonly BukuDaftarHadirScopeService $bukuDaftarHadirScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuDaftarHadir
    {
        $item = $this->bukuDaftarHadirRepository->find($id);
        $areaId = $this->bukuDaftarHadirScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bukuDaftarHadirScopeService->authorizeSameLevelAreaAndBudgetYear($item, $level, $areaId, $tahunAnggaran);
    }
}
