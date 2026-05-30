<?php

namespace App\Domains\Wilayah\Simulasi\UseCases;

use App\Domains\Wilayah\Simulasi\Models\BukuTamuSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuTamuSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Services\SimulasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuTamuSimulasiUseCase
{
    public function __construct(
        private readonly BukuTamuSimulasiRepositoryInterface $repository,
        private readonly SimulasiScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuTamuSimulasi
    {
        $user = auth()->user();
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        $model = $this->repository->find($id);

        return $this->scopeService->authorizeSameLevelAreaAndBudgetYear($model, $level, $areaId, $tahunAnggaran);
    }
}
