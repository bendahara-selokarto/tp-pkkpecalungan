<?php

namespace App\Domains\Wilayah\Simulasi\UseCases;

use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuNotulenSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Services\SimulasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuNotulenSimulasiUseCase
{
    public function __construct(
        private readonly BukuNotulenSimulasiRepositoryInterface $repository,
        private readonly SimulasiScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuNotulenSimulasi
    {
        $user = auth()->user();
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        $model = $this->repository->find($id);

        return $this->scopeService->authorizeSameLevelAreaAndBudgetYear($model, $level, $areaId, $tahunAnggaran);
    }
}
