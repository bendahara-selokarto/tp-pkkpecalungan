<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

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

    public function getDesaActivitiesByKecamatan(int $kecamatanAreaId)
    {
        return Activity::query()
            ->with(['area', 'creator'])
            ->where('level', 'desa')
            ->whereIn('area_id', function ($query) use ($kecamatanAreaId) {
                $query->select('id')
                    ->from((new Area())->getTable())
                    ->where('level', 'desa')
                    ->where('parent_id', $kecamatanAreaId);
            })
            ->latest('activity_date')
            ->latest('id')
            ->get();
    }

    public function queryScopedByUser(User $user): Builder
    {
        $query = Activity::query();

        if ($user->hasRole('super-admin')) {
            return $query;
        }

        $areaId = (int) $user->area_id;

        if ($user->scope === 'desa') {
            return $query->where('level', 'desa')->where('area_id', $areaId);
        }

        if ($user->scope === 'kecamatan') {
            $desaIds = Area::query()
                ->where('level', 'desa')
                ->where('parent_id', $areaId)
                ->pluck('id');

            return $query->where(function (Builder $scoped) use ($areaId, $desaIds) {
                $scoped->where(function (Builder $kecamatanScope) use ($areaId) {
                    $kecamatanScope->where('level', 'kecamatan')->where('area_id', $areaId);
                })->orWhere(function (Builder $desaScope) use ($desaIds) {
                    $desaScope->where('level', 'desa')->whereIn('area_id', $desaIds);
                });
            });
        }

        return $query->whereRaw('1 = 0');
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
