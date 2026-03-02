<?php

namespace App\Domains\Wilayah\BukuTamu\Repositories;

use App\Domains\Wilayah\BukuTamu\DTOs\BukuTamuData;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuTamuRepositoryInterface
{
    public function store(BukuTamuData $data): BukuTamu;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): BukuTamu;

    public function update(BukuTamu $bukuTamu, BukuTamuData $data): BukuTamu;

    public function delete(BukuTamu $bukuTamu): void;
}

