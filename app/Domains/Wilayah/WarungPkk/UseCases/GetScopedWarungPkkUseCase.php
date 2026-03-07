<?php

namespace App\Domains\Wilayah\WarungPkk\UseCases;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedWarungPkkUseCase
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly WarungPkkScopeService $warungPkkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): WarungPkk
    {
        $warungPkk = $this->warungPkkRepository->find($id);
        $areaId = $this->warungPkkScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->warungPkkScopeService->authorizeSameLevelAreaAndBudgetYear($warungPkk, $level, $areaId, $tahunAnggaran);
    }
}
