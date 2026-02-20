<?php

namespace App\Domains\Wilayah\DataWarga\Repositories;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use Illuminate\Support\Collection;

class DataWargaRepository implements DataWargaRepositoryInterface
{
    public function store(DataWargaData $data): DataWarga
    {
        return DataWarga::create([
            'dasawisma' => $data->dasawisma,
            'nama_kepala_keluarga' => $data->nama_kepala_keluarga,
            'alamat' => $data->alamat,
            'jumlah_warga_laki_laki' => $data->jumlah_warga_laki_laki,
            'jumlah_warga_perempuan' => $data->jumlah_warga_perempuan,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return DataWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataWarga
    {
        return DataWarga::findOrFail($id);
    }

    public function update(DataWarga $dataWarga, DataWargaData $data): DataWarga
    {
        $dataWarga->update([
            'dasawisma' => $data->dasawisma,
            'nama_kepala_keluarga' => $data->nama_kepala_keluarga,
            'alamat' => $data->alamat,
            'jumlah_warga_laki_laki' => $data->jumlah_warga_laki_laki,
            'jumlah_warga_perempuan' => $data->jumlah_warga_perempuan,
            'keterangan' => $data->keterangan,
        ]);

        return $dataWarga;
    }

    public function delete(DataWarga $dataWarga): void
    {
        $dataWarga->delete();
    }
}
