<?php

namespace App\Domains\Wilayah\DataKeluarga\Repositories;

use App\Domains\Wilayah\DataKeluarga\DTOs\DataKeluargaData;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataKeluargaRepository implements DataKeluargaRepositoryInterface
{
    public function store(DataKeluargaData $data): DataKeluarga
    {
        return DataKeluarga::create([
            'kategori_keluarga' => $data->kategori_keluarga,
            'jumlah_keluarga' => $data->jumlah_keluarga,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return DataKeluarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return DataKeluarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataKeluarga
    {
        return DataKeluarga::findOrFail($id);
    }

    public function update(DataKeluarga $dataKeluarga, DataKeluargaData $data): DataKeluarga
    {
        $dataKeluarga->update([
            'kategori_keluarga' => $data->kategori_keluarga,
            'jumlah_keluarga' => $data->jumlah_keluarga,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $dataKeluarga;
    }

    public function delete(DataKeluarga $dataKeluarga): void
    {
        $dataKeluarga->delete();
    }
}
