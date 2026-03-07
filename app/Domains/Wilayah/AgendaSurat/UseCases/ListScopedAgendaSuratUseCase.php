<?php

namespace App\Domains\Wilayah\AgendaSurat\UseCases;

use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Services\AgendaSuratScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedAgendaSuratUseCase
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly AgendaSuratScopeService $agendaSuratScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->agendaSuratScopeService->resolveCreatorIdFilterForList($level);

        return $this->agendaSuratRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->agendaSuratScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->agendaSuratScopeService->resolveCreatorIdFilterForList($level);

        return $this->agendaSuratRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $creatorIdFilter);
    }
}

