<?php

namespace App\Domains\Wilayah\Activities\Actions;

use App\Domains\Wilayah\Activities\DTOs\ActivityData;
use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\Repositories\ActivityRepositoryInterface;

class UpdateActivityAction
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository
    ) {
    }

    public function execute(Activity $activity, array $payload): Activity
    {
        $data = ActivityData::fromArray([
            'title' => $payload['title'],
            'nama_petugas' => $payload['nama_petugas'] ?? null,
            'jabatan_petugas' => $payload['jabatan_petugas'] ?? null,
            'description' => $payload['description'] ?? $payload['uraian'] ?? null,
            'uraian' => $payload['uraian'] ?? $payload['description'] ?? null,
            'level' => $activity->level,
            'area_id' => $activity->area_id,
            'created_by' => $activity->created_by,
            'activity_date' => $payload['activity_date'],
            'tempat_kegiatan' => $payload['tempat_kegiatan'] ?? null,
            'status' => $payload['status'],
            'tanda_tangan' => $payload['tanda_tangan'] ?? null,
        ]);

        return $this->activityRepository->update($activity, $data);
    }
}
