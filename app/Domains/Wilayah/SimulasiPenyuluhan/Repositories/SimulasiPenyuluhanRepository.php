<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Repositories;

use App\Domains\Wilayah\SimulasiPenyuluhan\DTOs\SimulasiPenyuluhanData;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SimulasiPenyuluhanRepository implements SimulasiPenyuluhanRepositoryInterface
{
    public function store(SimulasiPenyuluhanData $data): SimulasiPenyuluhan
    {
        return SimulasiPenyuluhan::create([
            'nama_kegiatan' => $data->nama_kegiatan,
            'jenis_simulasi_penyuluhan' => $data->jenis_simulasi_penyuluhan,
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_sosialisasi' => $data->jumlah_sosialisasi,
            'jumlah_kader_l' => $data->jumlah_kader_l,
            'jumlah_kader_p' => $data->jumlah_kader_p,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return SimulasiPenyuluhan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return SimulasiPenyuluhan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): SimulasiPenyuluhan
    {
        return SimulasiPenyuluhan::findOrFail($id);
    }

    public function update(SimulasiPenyuluhan $simulasiPenyuluhan, SimulasiPenyuluhanData $data): SimulasiPenyuluhan
    {
        $simulasiPenyuluhan->update([
            'nama_kegiatan' => $data->nama_kegiatan,
            'jenis_simulasi_penyuluhan' => $data->jenis_simulasi_penyuluhan,
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_sosialisasi' => $data->jumlah_sosialisasi,
            'jumlah_kader_l' => $data->jumlah_kader_l,
            'jumlah_kader_p' => $data->jumlah_kader_p,
            'keterangan' => $data->keterangan,
        ]);

        return $simulasiPenyuluhan;
    }

    public function delete(SimulasiPenyuluhan $simulasiPenyuluhan): void
    {
        $simulasiPenyuluhan->delete();
    }
}
