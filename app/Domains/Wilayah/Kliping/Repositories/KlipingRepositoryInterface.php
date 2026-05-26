<?php

namespace App\Domains\Wilayah\Kliping\Repositories;

use App\Domains\Wilayah\Kliping\DTOs\KlipingData;
use App\Domains\Wilayah\Kliping\Models\Kliping;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KlipingRepositoryInterface
{
    public function store(KlipingData $data): Kliping;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): Kliping;

    public function update(Kliping $kliping, KlipingData $data): Kliping;

    public function delete(Kliping $kliping): void;
}
