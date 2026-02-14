<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;
use App\Domains\Wilayah\Activities\Services\ActivityScopeService;

class CreateScopedActivityAction
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly ActivityScopeService $activityScopeService
    ) {
    }

    public function execute(array $payload, string $level): Activity
    {
        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'level' => $level,
            'area_id' => $this->activityScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
            'activity_date' => $payload['activity_date'],
            'status' => 'draft',
        ]);

        return $this->activityRepository->store($data);
    }
}
