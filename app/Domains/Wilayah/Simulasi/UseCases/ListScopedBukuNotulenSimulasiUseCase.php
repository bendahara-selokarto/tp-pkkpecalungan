<?php

namespace App\Domains\Wilayah\Simulasi\UseCases;

use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuNotulenSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Services\SimulasiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedBukuNotulenSimulasiUseCase
{
    public function __construct(
        private readonly BukuNotulenSimulasiRepositoryInterface $repository,
        private readonly SimulasiScopeService $scopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage = 15): LengthAwarePaginator
    {
        $user = auth()->user();
        $areaId = $this->scopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);

        return $this->repository->listScoped($level, $areaId, $tahunAnggaran, $perPage);
    }
}
