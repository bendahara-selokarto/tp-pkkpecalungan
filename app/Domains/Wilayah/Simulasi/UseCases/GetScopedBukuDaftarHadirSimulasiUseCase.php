<?php

namespace App\Domains\Wilayah\Simulasi\UseCases;

use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuDaftarHadirSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Services\SimulasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedBukuDaftarHadirSimulasiUseCase
{
    public function __construct(
        private readonly BukuDaftarHadirSimulasiRepositoryInterface $repository,
        private readonly SimulasiScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): BukuDaftarHadirSimulasi
    {
        $user = auth()->user();
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        $model = $this->repository->find($id);

        return $this->scopeService->authorizeSameLevelAreaAndBudgetYear($model, $level, $areaId, $tahunAnggaran);
    }
}
