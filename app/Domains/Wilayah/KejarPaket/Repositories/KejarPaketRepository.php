<?php

namespace App\Domains\Wilayah\KejarPaket\Repositories;

use App\Domains\Wilayah\KejarPaket\DTOs\KejarPaketData;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KejarPaketRepository implements KejarPaketRepositoryInterface
{
    public function store(KejarPaketData $data): KejarPaket
    {
        return KejarPaket::create([
            'nama_kejar_paket' => $data->nama_kejar_paket,
            'jenis_kejar_paket' => $data->jenis_kejar_paket,
            'jumlah_warga_belajar_l' => $data->jumlah_warga_belajar_l,
            'jumlah_warga_belajar_p' => $data->jumlah_warga_belajar_p,
            'jumlah_pengajar_l' => $data->jumlah_pengajar_l,
            'jumlah_pengajar_p' => $data->jumlah_pengajar_p,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return KejarPaket::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return KejarPaket::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): KejarPaket
    {
        return KejarPaket::findOrFail($id);
    }

    public function update(KejarPaket $kejarPaket, KejarPaketData $data): KejarPaket
    {
        $kejarPaket->update([
            'nama_kejar_paket' => $data->nama_kejar_paket,
            'jenis_kejar_paket' => $data->jenis_kejar_paket,
            'jumlah_warga_belajar_l' => $data->jumlah_warga_belajar_l,
            'jumlah_warga_belajar_p' => $data->jumlah_warga_belajar_p,
            'jumlah_pengajar_l' => $data->jumlah_pengajar_l,
            'jumlah_pengajar_p' => $data->jumlah_pengajar_p,
        ]);

        return $kejarPaket;
    }

    public function delete(KejarPaket $kejarPaket): void
    {
        $kejarPaket->delete();
    }
}





