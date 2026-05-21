<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Repositories;

use App\Domains\Wilayah\BukuEkspedisi\DTOs\BukuEkspedisiData;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuEkspedisiRepositoryInterface
{
    public function store(BukuEkspedisiData $data): BukuEkspedisi;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): BukuEkspedisi;

    public function update(BukuEkspedisi $bukuEkspedisi, BukuEkspedisiData $data): BukuEkspedisi;

    public function delete(BukuEkspedisi $bukuEkspedisi): void;
}
