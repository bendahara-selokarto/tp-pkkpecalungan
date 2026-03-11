<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\UseCases;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories\PelatihanKaderPokjaIiRepositoryInterface;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Services\PelatihanKaderPokjaIiScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedPelatihanKaderPokjaIiUseCase
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiRepositoryInterface $pelatihanKaderPokjaIiRepository,
        private readonly PelatihanKaderPokjaIiScopeService $pelatihanKaderPokjaIiScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->pelatihanKaderPokjaIiScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->pelatihanKaderPokjaIiRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }
}
