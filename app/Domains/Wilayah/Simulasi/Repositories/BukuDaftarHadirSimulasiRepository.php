<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuDaftarHadirSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BukuDaftarHadirSimulasiRepository implements BukuDaftarHadirSimulasiRepositoryInterface
{
    public function find(int $id): BukuDaftarHadirSimulasi
    {
        return BukuDaftarHadirSimulasi::findOrFail($id);
    }

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return BukuDaftarHadirSimulasi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->orderByDesc('attendance_date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function store(BukuDaftarHadirSimulasiData $data): BukuDaftarHadirSimulasi
    {
        return BukuDaftarHadirSimulasi::create($data->toArray());
    }

    public function update(BukuDaftarHadirSimulasi $model, BukuDaftarHadirSimulasiData $data): BukuDaftarHadirSimulasi
    {
        $model->update($data->toArray());
        return $model;
    }

    public function delete(BukuDaftarHadirSimulasi $model): bool
    {
        return $model->delete();
    }
}
