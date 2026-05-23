<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Repositories;

use App\Domains\Wilayah\BukuKonsultasi\DTOs\BukuKonsultasiData;
use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BukuKonsultasiRepository implements BukuKonsultasiRepositoryInterface
{
    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?string $group = null): LengthAwarePaginator
    {
        $query = BukuKonsultasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran);

        if ($group !== null) {
            $query->where('group', $group);
        }

        return $query->latest('activity_date')
            ->latest('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?BukuKonsultasi
    {
        return BukuKonsultasi::query()->find($id);
    }

    public function store(BukuKonsultasiData $data): BukuKonsultasi
    {
        return BukuKonsultasi::create([
            'activity_date' => $data->activity_date,
            'description' => $data->description,
            'disposition' => $data->disposition,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'group' => $data->group,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function update(BukuKonsultasi $bukuKonsultasi, BukuKonsultasiData $data): BukuKonsultasi
    {
        $bukuKonsultasi->update([
            'activity_date' => $data->activity_date,
            'description' => $data->description,
            'disposition' => $data->disposition,
            'tahun_anggaran' => $data->tahun_anggaran ?? $bukuKonsultasi->tahun_anggaran,
        ]);

        return $bukuKonsultasi->refresh();
    }

    public function delete(BukuKonsultasi $bukuKonsultasi): void
    {
        $bukuKonsultasi->delete();
    }
}
