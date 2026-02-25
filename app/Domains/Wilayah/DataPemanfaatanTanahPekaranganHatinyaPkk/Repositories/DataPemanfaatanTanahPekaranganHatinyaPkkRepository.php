<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\DTOs\DataPemanfaatanTanahPekaranganHatinyaPkkData;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataPemanfaatanTanahPekaranganHatinyaPkkRepository implements DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface
{
    public function store(DataPemanfaatanTanahPekaranganHatinyaPkkData $data): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        return DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => $data->kategori_pemanfaatan_lahan,
            'komoditi' => $data->komoditi,
            'jumlah_komoditi' => $data->jumlah_komoditi,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return DataPemanfaatanTanahPekaranganHatinyaPkk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return DataPemanfaatanTanahPekaranganHatinyaPkk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        return DataPemanfaatanTanahPekaranganHatinyaPkk::findOrFail($id);
    }

    public function update(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk, DataPemanfaatanTanahPekaranganHatinyaPkkData $data): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk->update([
            'kategori_pemanfaatan_lahan' => $data->kategori_pemanfaatan_lahan,
            'komoditi' => $data->komoditi,
            'jumlah_komoditi' => $data->jumlah_komoditi,
        ]);

        return $dataPemanfaatanTanahPekaranganHatinyaPkk;
    }

    public function delete(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): void
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk->delete();
    }
}


