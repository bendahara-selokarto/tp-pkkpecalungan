<?php

namespace App\Domains\Wilayah\KejarPaket\UseCases;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Services\KejarPaketScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedKejarPaketUseCase
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly KejarPaketScopeService $kejarPaketScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): KejarPaket
    {
        $kejarPaket = $this->kejarPaketRepository->find($id);
        $areaId = $this->kejarPaketScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->kejarPaketScopeService->authorizeSameLevelAreaAndBudgetYear($kejarPaket, $level, $areaId, $tahunAnggaran);
    }
}




