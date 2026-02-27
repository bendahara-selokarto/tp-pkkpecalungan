<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Repositories;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuNotulenRapatRepository implements BukuNotulenRapatRepositoryInterface
{
    public function store(BukuNotulenRapatData $data): BukuNotulenRapat
    {
        return BukuNotulenRapat::create([
            'entry_date' => $data->entry_date,
            'title' => $data->title,
            'person_name' => $data->person_name,
            'institution' => $data->institution,
            'description' => $data->description,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return BukuNotulenRapat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('entry_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return BukuNotulenRapat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('entry_date')
            ->latest('id')
            ->get();
    }

    public function find(int $id): BukuNotulenRapat
    {
        return BukuNotulenRapat::findOrFail($id);
    }

    public function update(BukuNotulenRapat $bukuNotulenRapat, BukuNotulenRapatData $data): BukuNotulenRapat
    {
        $bukuNotulenRapat->update([
            'entry_date' => $data->entry_date,
            'title' => $data->title,
            'person_name' => $data->person_name,
            'institution' => $data->institution,
            'description' => $data->description,
        ]);

        return $bukuNotulenRapat;
    }

    public function delete(BukuNotulenRapat $bukuNotulenRapat): void
    {
        $bukuNotulenRapat->delete();
    }
}
