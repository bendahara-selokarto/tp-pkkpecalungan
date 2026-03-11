<?php

namespace App\Domains\Wilayah\TutorKhusus\UseCases;

use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Domains\Wilayah\TutorKhusus\Repositories\TutorKhususRepositoryInterface;
use App\Domains\Wilayah\TutorKhusus\Services\TutorKhususScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class GetScopedTutorKhususUseCase
{
    public function __construct(
        private readonly TutorKhususRepositoryInterface $tutorKhususRepository,
        private readonly TutorKhususScopeService $tutorKhususScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(int $id, string $level): TutorKhusus
    {
        $tutorKhusus = $this->tutorKhususRepository->find($id);
        $areaId = $this->tutorKhususScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->tutorKhususScopeService->authorizeSameLevelAreaAndBudgetYear($tutorKhusus, $level, $areaId, $tahunAnggaran);
    }
}
