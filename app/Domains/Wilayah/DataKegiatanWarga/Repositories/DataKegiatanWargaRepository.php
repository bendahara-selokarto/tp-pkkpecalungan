<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Repositories;

use App\Domains\Wilayah\DataKegiatanWarga\DTOs\DataKegiatanWargaData;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataKegiatanWargaRepository implements DataKegiatanWargaRepositoryInterface
{
    public function store(DataKegiatanWargaData $data): DataKegiatanWarga
    {
        return DataKegiatanWarga::create([
            'kegiatan' => $data->kegiatan,
            'aktivitas' => $data->aktivitas,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataKegiatanWarga
    {
        return DataKegiatanWarga::findOrFail($id);
    }

    public function update(DataKegiatanWarga $dataKegiatanWarga, DataKegiatanWargaData $data): DataKegiatanWarga
    {
        $dataKegiatanWarga->update([
            'kegiatan' => $data->kegiatan,
            'aktivitas' => $data->aktivitas,
            'keterangan' => $data->keterangan,
        ]);

        return $dataKegiatanWarga;
    }

    public function delete(DataKegiatanWarga $dataKegiatanWarga): void
    {
        $dataKegiatanWarga->delete();
    }
}
