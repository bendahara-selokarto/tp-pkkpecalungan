<?php

namespace App\Domains\Wilayah\KaderKhusus\Repositories;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KaderKhususRepositoryInterface
{
    public function store(KaderKhususData $data): KaderKhusus;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?User $actor = null, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?User $actor = null, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): KaderKhusus;

    public function update(KaderKhusus $kaderKhusus, KaderKhususData $data): KaderKhusus;

    public function delete(KaderKhusus $kaderKhusus): void;
}
