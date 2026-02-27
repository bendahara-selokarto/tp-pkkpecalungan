<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Repositories;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuDaftarHadirRepository implements BukuDaftarHadirRepositoryInterface
{
    public function store(BukuDaftarHadirData $data): BukuDaftarHadir
    {
        return BukuDaftarHadir::create([
            'attendance_date' => $data->attendance_date,
            'activity_id' => $data->activity_id,
            'attendee_name' => $data->attendee_name,
            'institution' => $data->institution,
            'description' => $data->description,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return BukuDaftarHadir::query()
            ->with('activity:id,title,activity_date')
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('attendance_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return BukuDaftarHadir::query()
            ->with('activity:id,title,activity_date')
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('attendance_date')
            ->latest('id')
            ->get();
    }

    public function listActivityOptionsByLevelAndArea(string $level, int $areaId): Collection
    {
        return Activity::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->orderByDesc('activity_date')
            ->orderByDesc('id')
            ->get(['id', 'title', 'activity_date']);
    }

    public function find(int $id): BukuDaftarHadir
    {
        return BukuDaftarHadir::query()
            ->with('activity:id,title,activity_date')
            ->findOrFail($id);
    }

    public function update(BukuDaftarHadir $bukuDaftarHadir, BukuDaftarHadirData $data): BukuDaftarHadir
    {
        $bukuDaftarHadir->update([
            'attendance_date' => $data->attendance_date,
            'activity_id' => $data->activity_id,
            'attendee_name' => $data->attendee_name,
            'institution' => $data->institution,
            'description' => $data->description,
        ]);

        return $bukuDaftarHadir;
    }

    public function delete(BukuDaftarHadir $bukuDaftarHadir): void
    {
        $bukuDaftarHadir->delete();
    }
}
