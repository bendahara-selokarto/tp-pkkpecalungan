<?php

namespace App\Domains\Wilayah\ProgramPrioritas\UseCases;

use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ListScopedProgramPrioritasUseCase
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();
        $creatorIdFilter = $this->programPrioritasScopeService->resolveCreatorIdFilterForList($level);

        return $this->programPrioritasRepository->paginateByLevelAndArea($level, $areaId, $perPage, $creatorIdFilter);
    }

    public function executeAll(string $level): Collection
    {
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();
        $creatorIdFilter = $this->programPrioritasScopeService->resolveCreatorIdFilterForList($level);

        return $this->programPrioritasRepository->getByLevelAndArea($level, $areaId, $creatorIdFilter);
    }
}

