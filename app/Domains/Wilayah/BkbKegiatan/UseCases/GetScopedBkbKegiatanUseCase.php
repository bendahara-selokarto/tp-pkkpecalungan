<?php

namespace App\Domains\Wilayah\BkbKegiatan\UseCases;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Repositories\BkbKegiatanRepositoryInterface;
use App\Domains\Wilayah\BkbKegiatan\Services\BkbKegiatanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBkbKegiatanUseCase
{
    public function __construct(
        private readonly BkbKegiatanRepositoryInterface $bkbKegiatanRepository,
        private readonly BkbKegiatanScopeService $bkbKegiatanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BkbKegiatan
    {
        $bkbKegiatan = $this->bkbKegiatanRepository->find($id);
        $areaId = $this->bkbKegiatanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->bkbKegiatanScopeService->authorizeSameLevelAreaAndBudgetYear($bkbKegiatan, $level, $areaId, $tahunAnggaran);
    }
}
