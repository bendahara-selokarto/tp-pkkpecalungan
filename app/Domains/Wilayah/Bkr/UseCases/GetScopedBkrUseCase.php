<?php

namespace App\Domains\Wilayah\Bkr\UseCases;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBkrUseCase
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly BkrScopeService $bkrScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Bkr
    {
        $bkr = $this->bkrRepository->find($id);
        $areaId = $this->bkrScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bkrScopeService->authorizeSameLevelAreaAndBudgetYear($bkr, $level, $areaId, $tahunAnggaran);
    }
}

