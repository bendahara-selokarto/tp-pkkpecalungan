<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuNotulenSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BukuNotulenSimulasiRepository implements BukuNotulenSimulasiRepositoryInterface
{
    public function find(int $id): BukuNotulenSimulasi
    {
        return BukuNotulenSimulasi::findOrFail($id);
    }

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return BukuNotulenSimulasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(BukuNotulenSimulasiData $data): BukuNotulenSimulasi
    {
        return BukuNotulenSimulasi::create($data->toArray());
    }

    public function update(BukuNotulenSimulasi $model, BukuNotulenSimulasiData $data): BukuNotulenSimulasi
    {
        $model->update($data->toArray());
        return $model;
    }

    public function delete(BukuNotulenSimulasi $model): bool
    {
        return $model->delete();
    }
}
