<?php

namespace App\Domains\Wilayah\DataWarga\Repositories;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DataWargaRepositoryInterface
{
    public function store(DataWargaData $data): DataWarga;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataWarga;

    public function update(DataWarga $dataWarga, DataWargaData $data): DataWarga;

    public function delete(DataWarga $dataWarga): void;
}
