<?php

namespace App\Domains\Wilayah\WarungPkk\Repositories;

use App\Domains\Wilayah\WarungPkk\DTOs\WarungPkkData;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use Illuminate\Support\Collection;

class WarungPkkRepository implements WarungPkkRepositoryInterface
{
    public function store(WarungPkkData $data): WarungPkk
    {
        return WarungPkk::create([
            'nama_warung_pkk' => $data->nama_warung_pkk,
            'nama_pengelola' => $data->nama_pengelola,
            'komoditi' => $data->komoditi,
            'kategori' => $data->kategori,
            'volume' => $data->volume,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return WarungPkk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): WarungPkk
    {
        return WarungPkk::findOrFail($id);
    }

    public function update(WarungPkk $warungPkk, WarungPkkData $data): WarungPkk
    {
        $warungPkk->update([
            'nama_warung_pkk' => $data->nama_warung_pkk,
            'nama_pengelola' => $data->nama_pengelola,
            'komoditi' => $data->komoditi,
            'kategori' => $data->kategori,
            'volume' => $data->volume,
        ]);

        return $warungPkk;
    }

    public function delete(WarungPkk $warungPkk): void
    {
        $warungPkk->delete();
    }
}
