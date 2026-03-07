<?php

namespace App\Domains\Wilayah\Activities\UseCases;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class GetScopedActivityUseCase
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {}

    public function execute(int $id, string $level): Activity
    {
        $activity = $this->activityRepository->find($id);
        $user = $this->activityScopeService->requireAuthenticatedUser();
        $areaId = $this->activityScopeService->requireUserAreaId();
        $tahunAnggaran = $this->activityScopeService->requireActiveBudgetYear();
        $activity = $this->activityScopeService->authorizeSameLevelAreaAndBudgetYear($activity, $level, $areaId, $tahunAnggaran);

        return $this->activityScopeService->authorizeRoleScopedActivity($user, $activity, $level);
    }
}
