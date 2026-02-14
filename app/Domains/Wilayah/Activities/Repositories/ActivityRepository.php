<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\DTOs\ActivityData;

class ActivityRepository
{
    public function store(ActivityData $data): Activity
    {
        return Activity::create([
            'title'         => $data->title,
            'description'   => $data->description,
            'level'         => $data->level,
            'area_id'       => $data->area_id,
            'created_by'    => $data->created_by,
            'activity_date' => $data->activity_date,
            'status'        => $data->status,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId)
    {
        return Activity::where('level', $level)
            ->where('area_id', $areaId)
            ->get();
    }

    public function find(int $id): Activity
    {
        return Activity::findOrFail($id);
    }

    public function update(Activity $activity, ActivityData $data): Activity
    {
        $activity->update([
            'title'         => $data->title,
            'description'   => $data->description,
            'activity_date' => $data->activity_date,
            'status'        => $data->status,
        ]);

        return $activity;
    }

    public function delete(Activity $activity): void
    {
        $activity->delete();
    }
}
