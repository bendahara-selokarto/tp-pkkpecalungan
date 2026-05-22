<?php

namespace App\Domains\Wilayah\BukuAgendaSk\UseCases;

use App\Domains\Wilayah\BukuAgendaSk\Repositories\BukuAgendaSkRepositoryInterface;
use App\Domains\Wilayah\BukuAgendaSk\Services\BukuAgendaSkScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedBukuAgendaSkUseCase
{
    public function __construct(
        private readonly BukuAgendaSkRepositoryInterface $bukuAgendaSkRepository,
        private readonly BukuAgendaSkScopeService $bukuAgendaSkScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        $areaId = $this->bukuAgendaSkScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bukuAgendaSkRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level, ?int $creatorIdFilter = null): Collection
    {
        $areaId = $this->bukuAgendaSkScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->bukuAgendaSkRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}
