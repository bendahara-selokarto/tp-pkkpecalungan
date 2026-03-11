<?php

namespace App\Domains\Wilayah\BkbKegiatan\Repositories;

use App\Domains\Wilayah\BkbKegiatan\DTOs\BkbKegiatanData;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BkbKegiatanRepository implements BkbKegiatanRepositoryInterface
{
    public function store(BkbKegiatanData $data): BkbKegiatan
    {
        return BkbKegiatan::create([
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_ibu_peserta' => $data->jumlah_ibu_peserta,
            'jumlah_ape_set' => $data->jumlah_ape_set,
            'jumlah_kelompok_simulasi' => $data->jumlah_kelompok_simulasi,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return BkbKegiatan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return BkbKegiatan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): BkbKegiatan
    {
        return BkbKegiatan::findOrFail($id);
    }

    public function update(BkbKegiatan $bkbKegiatan, BkbKegiatanData $data): BkbKegiatan
    {
        $bkbKegiatan->update([
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_ibu_peserta' => $data->jumlah_ibu_peserta,
            'jumlah_ape_set' => $data->jumlah_ape_set,
            'jumlah_kelompok_simulasi' => $data->jumlah_kelompok_simulasi,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $bkbKegiatan;
    }

    public function delete(BkbKegiatan $bkbKegiatan): void
    {
        $bkbKegiatan->delete();
    }
}
