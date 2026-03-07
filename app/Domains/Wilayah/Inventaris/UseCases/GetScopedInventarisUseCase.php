<?php

namespace App\Domains\Wilayah\Inventaris\UseCases;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedInventarisUseCase
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): Inventaris
    {
        $inventaris = $this->inventarisRepository->find($id);
        $areaId = $this->inventarisScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->inventarisScopeService->authorizeSameLevelAreaAndBudgetYear($inventaris, $level, $areaId, $tahunAnggaran);
    }
}
