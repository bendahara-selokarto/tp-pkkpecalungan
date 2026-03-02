<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Repositories;

use App\Domains\Wilayah\BukuNotulenRapat\DTOs\BukuNotulenRapatData;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuNotulenRapatRepositoryInterface
{
    public function store(BukuNotulenRapatData $data): BukuNotulenRapat;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): BukuNotulenRapat;

    public function update(BukuNotulenRapat $bukuNotulenRapat, BukuNotulenRapatData $data): BukuNotulenRapat;

    public function delete(BukuNotulenRapat $bukuNotulenRapat): void;
}

