<?php

namespace App\Domains\Wilayah\Paar\UseCases;

use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPaarUseCase
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly PaarScopeService $paarScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): Paar
    {
        $paar = $this->paarRepository->find($id);
        $areaId = $this->paarScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->paarScopeService->authorizeSameLevelAreaAndBudgetYear($paar, $level, $areaId, $tahunAnggaran);
    }
}
