<?php

namespace App\Domains\Wilayah\BukuTamu\Repositories;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuTamuRepository implements BukuTamuRepositoryInterface
{
    public function store(BukuTamuData $data): BukuTamu
    {
        return BukuTamu::create([
            'visit_date' => $data->visit_date,
            'guest_name' => $data->guest_name,
            'purpose' => $data->purpose,
            'institution' => $data->institution,
            'description' => $data->description,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return BukuTamu::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('visit_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return BukuTamu::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('visit_date')
            ->latest('id')
            ->get();
    }

    public function find(int $id): BukuTamu
    {
        return BukuTamu::findOrFail($id);
    }

    public function update(BukuTamu $bukuTamu, BukuTamuData $data): BukuTamu
    {
        $bukuTamu->update([
            'visit_date' => $data->visit_date,
            'guest_name' => $data->guest_name,
            'purpose' => $data->purpose,
            'institution' => $data->institution,
            'description' => $data->description,
        ]);

        return $bukuTamu;
    }

    public function delete(BukuTamu $bukuTamu): void
    {
        $bukuTamu->delete();
    }
}
