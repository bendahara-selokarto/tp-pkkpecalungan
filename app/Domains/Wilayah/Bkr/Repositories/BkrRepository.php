<?php

namespace App\Domains\Wilayah\Bkr\Repositories;

use App\Domains\Wilayah\Bkr\DTOs\BkrData;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BkrRepository implements BkrRepositoryInterface
{
    public function store(BkrData $data): Bkr
    {
        return Bkr::create([
            'desa' => $data->desa,
            'nama_bkr' => $data->nama_bkr,
            'no_tgl_sk' => $data->no_tgl_sk,
            'nama_ketua_kelompok' => $data->nama_ketua_kelompok,
            'jumlah_anggota' => $data->jumlah_anggota,
            'kegiatan' => $data->kegiatan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return Bkr::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return Bkr::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): Bkr
    {
        return Bkr::findOrFail($id);
    }

    public function update(Bkr $bkr, BkrData $data): Bkr
    {
        $bkr->update([
            'desa' => $data->desa,
            'nama_bkr' => $data->nama_bkr,
            'no_tgl_sk' => $data->no_tgl_sk,
            'nama_ketua_kelompok' => $data->nama_ketua_kelompok,
            'jumlah_anggota' => $data->jumlah_anggota,
            'kegiatan' => $data->kegiatan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $bkr;
    }

    public function delete(Bkr $bkr): void
    {
        $bkr->delete();
    }
}
