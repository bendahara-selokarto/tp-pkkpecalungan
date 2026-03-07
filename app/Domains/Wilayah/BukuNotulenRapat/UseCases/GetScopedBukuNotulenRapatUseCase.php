<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\UseCases;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Services\BukuNotulenRapatScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuNotulenRapatUseCase
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly BukuNotulenRapatScopeService $bukuNotulenRapatScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuNotulenRapat
    {
        $item = $this->bukuNotulenRapatRepository->find($id);
        $areaId = $this->bukuNotulenRapatScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bukuNotulenRapatScopeService->authorizeSameLevelAreaAndBudgetYear($item, $level, $areaId, $tahunAnggaran);
    }
}
