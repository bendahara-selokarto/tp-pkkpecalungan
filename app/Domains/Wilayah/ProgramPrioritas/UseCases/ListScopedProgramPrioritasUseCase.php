<?php

namespace App\Domains\Wilayah\ProgramPrioritas\UseCases;

use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedProgramPrioritasUseCase
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $actor = $this->programPrioritasScopeService->requireAuthenticatedUser();
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->programPrioritasScopeService->resolveCreatorIdFilterForList($level);
        $allowedGroups = $this->programPrioritasScopeService->resolveProgramPrioritasGroupsForUser($actor);

        return $this->programPrioritasRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage, $allowedGroups, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $actor = $this->programPrioritasScopeService->requireAuthenticatedUser();
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();
        $creatorIdFilter = $this->programPrioritasScopeService->resolveCreatorIdFilterForList($level);
        $allowedGroups = $this->programPrioritasScopeService->resolveProgramPrioritasGroupsForUser($actor);

        return $this->programPrioritasRepository->getByLevelAndArea($level, $areaId, $tahunAnggaran, $allowedGroups, $creatorIdFilter);
    }
}
