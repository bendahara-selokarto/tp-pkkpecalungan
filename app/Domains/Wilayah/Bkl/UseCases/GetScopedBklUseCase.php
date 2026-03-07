<?php

namespace App\Domains\Wilayah\Bkl\UseCases;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBklUseCase
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly BklScopeService $bklScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Bkl
    {
        $bkl = $this->bklRepository->find($id);
        $areaId = $this->bklScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bklScopeService->authorizeSameLevelAreaAndBudgetYear($bkl, $level, $areaId, $tahunAnggaran);
    }
}
