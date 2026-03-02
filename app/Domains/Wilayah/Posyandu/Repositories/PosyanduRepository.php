<?php

namespace App\Domains\Wilayah\Posyandu\Repositories;

use App\Domains\Wilayah\Posyandu\DTOs\PosyanduData;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PosyanduRepository implements PosyanduRepositoryInterface
{
    public function store(PosyanduData $data): Posyandu
    {
        return Posyandu::create([
            'nama_posyandu' => $data->nama_posyandu,
            'nama_pengelola' => $data->nama_pengelola,
            'nama_sekretaris' => $data->nama_sekretaris,
            'jenis_posyandu' => $data->jenis_posyandu,
            'jumlah_kader' => $data->jumlah_kader,
            'jenis_kegiatan' => $data->jenis_kegiatan,
            'frekuensi_layanan' => $data->frekuensi_layanan,
            'jumlah_pengunjung_l' => $data->jumlah_pengunjung_l,
            'jumlah_pengunjung_p' => $data->jumlah_pengunjung_p,
            'jumlah_petugas_l' => $data->jumlah_petugas_l,
            'jumlah_petugas_p' => $data->jumlah_petugas_p,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return Posyandu::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return Posyandu::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): Posyandu
    {
        return Posyandu::findOrFail($id);
    }

    public function update(Posyandu $posyandu, PosyanduData $data): Posyandu
    {
        $posyandu->update([
            'nama_posyandu' => $data->nama_posyandu,
            'nama_pengelola' => $data->nama_pengelola,
            'nama_sekretaris' => $data->nama_sekretaris,
            'jenis_posyandu' => $data->jenis_posyandu,
            'jumlah_kader' => $data->jumlah_kader,
            'jenis_kegiatan' => $data->jenis_kegiatan,
            'frekuensi_layanan' => $data->frekuensi_layanan,
            'jumlah_pengunjung_l' => $data->jumlah_pengunjung_l,
            'jumlah_pengunjung_p' => $data->jumlah_pengunjung_p,
            'jumlah_petugas_l' => $data->jumlah_petugas_l,
            'jumlah_petugas_p' => $data->jumlah_petugas_p,
            'keterangan' => $data->keterangan,
        ]);

        return $posyandu;
    }

    public function delete(Posyandu $posyandu): void
    {
        $posyandu->delete();
    }
}





