<?php

namespace App\Domains\Wilayah\ProgramPrioritas\UseCases;

use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedProgramPrioritasUseCase
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function execute(int $id, string $level): ProgramPrioritas
    {
        $programPrioritas = $this->programPrioritasRepository->find($id);
        $areaId = $this->programPrioritasScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->requireForAuthenticatedUser();

        return $this->programPrioritasScopeService->authorizeSameLevelAreaAndBudgetYear($programPrioritas, $level, $areaId, $tahunAnggaran);
    }
}
