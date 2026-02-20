<?php

namespace App\Domains\Wilayah\ProgramPrioritas\UseCases;

use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;

class ListScopedProgramPrioritasUseCase
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();

        return $this->programPrioritasRepository->getByLevelAndArea($level, $areaId);
    }
}
