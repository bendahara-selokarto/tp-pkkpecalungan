<?php

namespace App\Domains\Wilayah\TamanBacaan\UseCases;

use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Services\TamanBacaanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedTamanBacaanUseCase
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly TamanBacaanScopeService $tamanBacaanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): TamanBacaan
    {
        $tamanBacaan = $this->tamanBacaanRepository->find($id);
        $areaId = $this->tamanBacaanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->tamanBacaanScopeService->authorizeSameLevelAreaAndBudgetYear($tamanBacaan, $level, $areaId, $tahunAnggaran);
    }
}

