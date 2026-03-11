<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\UseCases;

use App\Domains\Wilayah\PraKoperasiUp2k\Repositories\PraKoperasiUp2kRepositoryInterface;
use App\Domains\Wilayah\PraKoperasiUp2k\Services\PraKoperasiUp2kScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedPraKoperasiUp2kUseCase
{
    public function __construct(
        private readonly PraKoperasiUp2kRepositoryInterface $praKoperasiUp2kRepository,
        private readonly PraKoperasiUp2kScopeService $praKoperasiUp2kScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->praKoperasiUp2kScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->praKoperasiUp2kRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }
}
