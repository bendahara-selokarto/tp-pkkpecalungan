<?php

namespace App\Domains\Wilayah\Koperasi\Repositories;

use App\Domains\Wilayah\Koperasi\DTOs\KoperasiData;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KoperasiRepositoryInterface
{
    public function store(KoperasiData $data): Koperasi;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Koperasi;

    public function update(Koperasi $koperasi, KoperasiData $data): Koperasi;

    public function delete(Koperasi $koperasi): void;
}



