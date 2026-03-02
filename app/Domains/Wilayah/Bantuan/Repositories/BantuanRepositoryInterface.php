<?php

namespace App\Domains\Wilayah\Bantuan\Repositories;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BantuanRepositoryInterface
{
    public function store(BantuanData $data): Bantuan;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): Bantuan;

    public function update(Bantuan $bantuan, BantuanData $data): Bantuan;

    public function delete(Bantuan $bantuan): void;
}


