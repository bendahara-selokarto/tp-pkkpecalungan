<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\UseCases;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories\PelatihanKaderPokjaIiRepositoryInterface;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Services\PelatihanKaderPokjaIiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedPelatihanKaderPokjaIiUseCase
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiRepositoryInterface $pelatihanKaderPokjaIiRepository,
        private readonly PelatihanKaderPokjaIiScopeService $pelatihanKaderPokjaIiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): PelatihanKaderPokjaIi
    {
        $pelatihanKaderPokjaIi = $this->pelatihanKaderPokjaIiRepository->find($id);
        $areaId = $this->pelatihanKaderPokjaIiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->pelatihanKaderPokjaIiScopeService->authorizeSameLevelAreaAndBudgetYear($pelatihanKaderPokjaIi, $level, $areaId, $tahunAnggaran);
    }
}
