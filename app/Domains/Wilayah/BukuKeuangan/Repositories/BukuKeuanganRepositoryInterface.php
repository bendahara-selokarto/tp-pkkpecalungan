<?php

namespace App\Domains\Wilayah\BukuKeuangan\Repositories;

use App\Domains\Wilayah\BukuKeuangan\DTOs\BukuKeuanganData;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuKeuanganRepositoryInterface
{
    public function store(BukuKeuanganData $data): BukuKeuangan;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): BukuKeuangan;

    public function update(BukuKeuangan $bukuKeuangan, BukuKeuanganData $data): BukuKeuangan;

    public function delete(BukuKeuangan $bukuKeuangan): void;
}
