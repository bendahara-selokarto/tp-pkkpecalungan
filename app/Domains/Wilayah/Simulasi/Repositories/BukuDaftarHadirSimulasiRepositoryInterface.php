<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuDaftarHadirSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BukuDaftarHadirSimulasiRepositoryInterface
{
    public function find(int $id): BukuDaftarHadirSimulasi;

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function store(BukuDaftarHadirSimulasiData $data): BukuDaftarHadirSimulasi;

    public function update(BukuDaftarHadirSimulasi $model, BukuDaftarHadirSimulasiData $data): BukuDaftarHadirSimulasi;

    public function delete(BukuDaftarHadirSimulasi $model): bool;
}
