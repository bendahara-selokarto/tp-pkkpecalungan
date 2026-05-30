<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuTamuSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuTamuSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BukuTamuSimulasiRepositoryInterface
{
    public function find(int $id): BukuTamuSimulasi;

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function store(BukuTamuSimulasiData $data): BukuTamuSimulasi;

    public function update(BukuTamuSimulasi $model, BukuTamuSimulasiData $data): BukuTamuSimulasi;

    public function delete(BukuTamuSimulasi $model): bool;
}
