<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Repositories;

use App\Domains\Wilayah\BukuDaftarHadir\DTOs\BukuDaftarHadirData;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuDaftarHadirRepositoryInterface
{
    public function store(BukuDaftarHadirData $data): BukuDaftarHadir;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function listActivityOptionsByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): BukuDaftarHadir;

    public function update(BukuDaftarHadir $bukuDaftarHadir, BukuDaftarHadirData $data): BukuDaftarHadir;

    public function delete(BukuDaftarHadir $bukuDaftarHadir): void;
}

