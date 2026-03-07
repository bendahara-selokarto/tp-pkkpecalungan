<?php

namespace App\Domains\Wilayah\Posyandu\UseCases;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPosyanduUseCase
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly PosyanduScopeService $posyanduScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Posyandu
    {
        $posyandu = $this->posyanduRepository->find($id);
        $areaId = $this->posyanduScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->posyanduScopeService->authorizeSameLevelAreaAndBudgetYear($posyandu, $level, $areaId, $tahunAnggaran);
    }
}




