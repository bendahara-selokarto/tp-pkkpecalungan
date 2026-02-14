<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepository;

class UpdateActivityAction
{
    public function __construct(
        private readonly ActivityRepository $activityRepository
    ) {
    }

    public function execute(Activity $activity, array $payload): Activity
    {
        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'level' => $activity->level,
            'area_id' => $activity->area_id,
            'created_by' => $activity->created_by,
            'activity_date' => $payload['activity_date'],
            'status' => $payload['status'],
        ]);

        return $this->activityRepository->update($activity, $data);
    }
}
