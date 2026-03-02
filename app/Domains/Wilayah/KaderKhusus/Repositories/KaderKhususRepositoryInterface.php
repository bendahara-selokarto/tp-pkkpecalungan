<?php

namespace App\Domains\Wilayah\KaderKhusus\Repositories;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KaderKhususRepositoryInterface
{
    public function store(KaderKhususData $data): KaderKhusus;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): KaderKhusus;

    public function update(KaderKhusus $kaderKhusus, KaderKhususData $data): KaderKhusus;

    public function delete(KaderKhusus $kaderKhusus): void;
}

