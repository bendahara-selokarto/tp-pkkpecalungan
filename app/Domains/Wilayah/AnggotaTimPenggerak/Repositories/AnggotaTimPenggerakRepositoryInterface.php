<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Repositories;

use App\Domains\Wilayah\AnggotaTimPenggerak\DTOs\AnggotaTimPenggerakData;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AnggotaTimPenggerakRepositoryInterface
{
    public function store(AnggotaTimPenggerakData $data): AnggotaTimPenggerak;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): AnggotaTimPenggerak;

    public function update(AnggotaTimPenggerak $anggotaTimPenggerak, AnggotaTimPenggerakData $data): AnggotaTimPenggerak;

    public function delete(AnggotaTimPenggerak $anggotaTimPenggerak): void;
}



