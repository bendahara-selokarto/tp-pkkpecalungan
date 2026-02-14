<?php

namespace App\Domains\Wilayah\Activities\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\DTOs\ActivityData;

class ActivityRepository
{
    public function store(ActivityData $data): Activity
    {
        return Activity::create([
            'nama_kegiatan' => $data->nama_kegiatan,
            'deskripsi'     => $data->deskripsi,
            'tanggal'       => $data->tanggal,
            'desa_id'       => $data->desa_id,
            'kecamatan_id'  => $data->kecamatan_id,
            'created_by'    => $data->created_by,
        ]);
    }

    public function getByDesa(int $desaId)
    {
        return Activity::where('desa_id', $desaId)->get();
    }

    public function find(int $id): Activity
    {
        return Activity::findOrFail($id);
    }

    public function update(Activity $activity, ActivityData $data): Activity
    {
        $activity->update([
            'nama_kegiatan' => $data->nama_kegiatan,
            'deskripsi'     => $data->deskripsi,
            'tanggal'       => $data->tanggal,
        ]);

        return $activity;
    }

    public function delete(Activity $activity): void
    {
        $activity->delete();
    }
}
