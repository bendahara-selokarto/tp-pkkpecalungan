<?php

namespace App\Domains\Wilayah\LiterasiWarga\UseCases;

use App\Domains\Wilayah\LiterasiWarga\Repositories\LiterasiWargaRepositoryInterface;
use App\Domains\Wilayah\LiterasiWarga\Services\LiterasiWargaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedLiterasiWargaUseCase
{
    public function __construct(
        private readonly LiterasiWargaRepositoryInterface $literasiWargaRepository,
        private readonly LiterasiWargaScopeService $literasiWargaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->literasiWargaScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->literasiWargaRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }
}
