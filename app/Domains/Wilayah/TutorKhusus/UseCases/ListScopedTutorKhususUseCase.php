<?php

namespace App\Domains\Wilayah\TutorKhusus\UseCases;

use App\Domains\Wilayah\TutorKhusus\Repositories\TutorKhususRepositoryInterface;
use App\Domains\Wilayah\TutorKhusus\Services\TutorKhususScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListScopedTutorKhususUseCase
{
    public function __construct(
        private readonly TutorKhususRepositoryInterface $tutorKhususRepository,
        private readonly TutorKhususScopeService $tutorKhususScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {
    }

    public function execute(string $level, int $perPage): LengthAwarePaginator
    {
        $areaId = $this->tutorKhususScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser(auth()->user());

        return $this->tutorKhususRepository->paginateByLevelAndArea($level, $areaId, $tahunAnggaran, $perPage);
    }
}
