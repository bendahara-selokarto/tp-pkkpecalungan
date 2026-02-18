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
    ) {
    }

    public function execute(int $id, string $level): Activity
    {
        $activity = $this->activityRepository->find($id);
        $areaId = $this->activityScopeService->requireUserAreaId();

        return $this->activityScopeService->authorizeSameLevelAndArea($activity, $level, $areaId);
    }
}
