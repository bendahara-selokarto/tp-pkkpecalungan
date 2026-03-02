<?php

namespace App\Domains\Wilayah\Inventaris\Repositories;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InventarisRepositoryInterface
{
    public function store(InventarisData $data): Inventaris;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): Inventaris;

    public function update(Inventaris $inventaris, InventarisData $data): Inventaris;

    public function delete(Inventaris $inventaris): void;
}


