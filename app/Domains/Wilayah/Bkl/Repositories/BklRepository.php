<?php

namespace App\Domains\Wilayah\Bkl\Repositories;

use App\Domains\Wilayah\Bkl\DTOs\BklData;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use Illuminate\Support\Collection;

class BklRepository implements BklRepositoryInterface
{
    public function store(BklData $data): Bkl
    {
        return Bkl::create([
            'desa' => $data->desa,
            'nama_bkl' => $data->nama_bkl,
            'no_tgl_sk' => $data->no_tgl_sk,
            'nama_ketua_kelompok' => $data->nama_ketua_kelompok,
            'jumlah_anggota' => $data->jumlah_anggota,
            'kegiatan' => $data->kegiatan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return Bkl::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): Bkl
    {
        return Bkl::findOrFail($id);
    }

    public function update(Bkl $bkl, BklData $data): Bkl
    {
        $bkl->update([
            'desa' => $data->desa,
            'nama_bkl' => $data->nama_bkl,
            'no_tgl_sk' => $data->no_tgl_sk,
            'nama_ketua_kelompok' => $data->nama_ketua_kelompok,
            'jumlah_anggota' => $data->jumlah_anggota,
            'kegiatan' => $data->kegiatan,
        ]);

        return $bkl;
    }

    public function delete(Bkl $bkl): void
    {
        $bkl->delete();
    }
}
