<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Repositories;

use App\Domains\Wilayah\DataPelatihanKader\DTOs\DataPelatihanKaderData;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DataPelatihanKaderRepositoryInterface
{
    public function store(DataPelatihanKaderData $data): DataPelatihanKader;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataPelatihanKader;

    public function update(DataPelatihanKader $dataPelatihanKader, DataPelatihanKaderData $data): DataPelatihanKader;

    public function delete(DataPelatihanKader $dataPelatihanKader): void;
}




