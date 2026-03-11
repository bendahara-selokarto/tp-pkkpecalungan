<?php

namespace App\Domains\Wilayah\BkbKegiatan\UseCases;

use App\Domains\Wilayah\BkbKegiatan\Repositories\BkbKegiatanRepositoryInterface;
use App\Domains\Wilayah\BkbKegiatan\Services\BkbKegiatanScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedBkbKegiatanUseCase
{
    public function __construct(
        private readonly BkbKegiatanRepositoryInterface $bkbKegiatanRepository,
        private readonly BkbKegiatanScopeService $bkbKegiatanScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->bkbKegiatanScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->bkbKegiatanRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }
}
