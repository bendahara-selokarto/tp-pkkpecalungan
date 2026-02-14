<?php

namespace App\Domains\Wilayah\Activities\UseCases;

use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class ListScopedActivitiesUseCase
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function execute(string $level)
    {
        $areaId = $this->activityScopeService->requireUserAreaId();

        return $this->activityRepository->getByLevelAndArea($level, $areaId);
    }
}
