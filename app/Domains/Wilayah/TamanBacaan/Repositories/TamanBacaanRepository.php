<?php

namespace App\Domains\Wilayah\TamanBacaan\Repositories;

use App\Domains\Wilayah\TamanBacaan\DTOs\TamanBacaanData;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TamanBacaanRepository implements TamanBacaanRepositoryInterface
{
    public function store(TamanBacaanData $data): TamanBacaan
    {
        return TamanBacaan::create([
            'nama_taman_bacaan' => $data->nama_taman_bacaan,
            'nama_pengelola' => $data->nama_pengelola,
            'jumlah_buku_bacaan' => $data->jumlah_buku_bacaan,
            'jenis_buku' => $data->jenis_buku,
            'kategori' => $data->kategori,
            'jumlah' => $data->jumlah,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return TamanBacaan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return TamanBacaan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): TamanBacaan
    {
        return TamanBacaan::findOrFail($id);
    }

    public function update(TamanBacaan $tamanBacaan, TamanBacaanData $data): TamanBacaan
    {
        $tamanBacaan->update([
            'nama_taman_bacaan' => $data->nama_taman_bacaan,
            'nama_pengelola' => $data->nama_pengelola,
            'jumlah_buku_bacaan' => $data->jumlah_buku_bacaan,
            'jenis_buku' => $data->jenis_buku,
            'kategori' => $data->kategori,
            'jumlah' => $data->jumlah,
        ]);

        return $tamanBacaan;
    }

    public function delete(TamanBacaan $tamanBacaan): void
    {
        $tamanBacaan->delete();
    }
}

