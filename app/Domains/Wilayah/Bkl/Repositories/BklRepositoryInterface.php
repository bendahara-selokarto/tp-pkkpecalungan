<?php

namespace App\Domains\Wilayah\Bkl\Repositories;

use App\Domains\Wilayah\Bkl\DTOs\BklData;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BklRepositoryInterface
{
    public function store(BklData $data): Bkl;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Bkl;

    public function update(Bkl $bkl, BklData $data): Bkl;

    public function delete(Bkl $bkl): void;
}
