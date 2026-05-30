<?php

namespace App\Domains\Wilayah\Simulasi\Repositories;

use App\Domains\Wilayah\Simulasi\DTOs\BukuNotulenSimulasiData;
use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BukuNotulenSimulasiRepositoryInterface
{
    public function find(int $id): BukuNotulenSimulasi;

    public function listScoped(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function store(BukuNotulenSimulasiData $data): BukuNotulenSimulasi;

    public function update(BukuNotulenSimulasi $model, BukuNotulenSimulasiData $data): BukuNotulenSimulasi;

    public function delete(BukuNotulenSimulasi $model): bool;
}
