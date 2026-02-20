<?php

namespace App\Domains\Wilayah\ProgramPrioritas\UseCases;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;

class GetScopedProgramPrioritasUseCase
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function execute(int $id, string $level): ProgramPrioritas
    {
        $programPrioritas = $this->programPrioritasRepository->find($id);
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();

        return $this->programPrioritasScopeService->authorizeSameLevelAndArea($programPrioritas, $level, $areaId);
    }
}
