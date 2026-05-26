<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuTamuSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuTamuSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BukuTamuSimulasiRepository implements BukuTamuSimulasiRepositoryInterface
{
    public function find(int $id): BukuTamuSimulasi
    {
        return BukuTamuSimulasi::findOrFail($id);
    }

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return BukuTamuSimulasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(BukuTamuSimulasiData $data): BukuTamuSimulasi
    {
        return BukuTamuSimulasi::create($data->toArray());
    }

    public function update(BukuTamuSimulasi $model, BukuTamuSimulasiData $data): BukuTamuSimulasi
    {
        $model->update($data->toArray());
        return $model;
    }

    public function delete(BukuTamuSimulasi $model): bool
    {
        return $model->delete();
    }
}
