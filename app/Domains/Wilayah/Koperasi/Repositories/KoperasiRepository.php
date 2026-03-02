<?php

namespace App\Domains\Wilayah\Koperasi\Repositories;

use App\Domains\Wilayah\Koperasi\DTOs\KoperasiData;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KoperasiRepository implements KoperasiRepositoryInterface
{
    public function store(KoperasiData $data): Koperasi
    {
        return Koperasi::create([
            'nama_koperasi' => $data->nama_koperasi,
            'jenis_usaha' => $data->jenis_usaha,
            'berbadan_hukum' => $data->berbadan_hukum,
            'belum_berbadan_hukum' => $data->belum_berbadan_hukum,
            'jumlah_anggota_l' => $data->jumlah_anggota_l,
            'jumlah_anggota_p' => $data->jumlah_anggota_p,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return Koperasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return Koperasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): Koperasi
    {
        return Koperasi::findOrFail($id);
    }

    public function update(Koperasi $koperasi, KoperasiData $data): Koperasi
    {
        $koperasi->update([
            'nama_koperasi' => $data->nama_koperasi,
            'jenis_usaha' => $data->jenis_usaha,
            'berbadan_hukum' => $data->berbadan_hukum,
            'belum_berbadan_hukum' => $data->belum_berbadan_hukum,
            'jumlah_anggota_l' => $data->jumlah_anggota_l,
            'jumlah_anggota_p' => $data->jumlah_anggota_p,
        ]);

        return $koperasi;
    }

    public function delete(Koperasi $koperasi): void
    {
        $koperasi->delete();
    }
}


