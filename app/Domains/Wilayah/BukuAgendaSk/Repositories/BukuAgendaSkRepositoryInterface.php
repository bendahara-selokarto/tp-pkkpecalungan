<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Repositories;

use App\Domains\Wilayah\BukuAgendaSk\DTOs\BukuAgendaSkData;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BukuAgendaSkRepositoryInterface
{
    public function store(BukuAgendaSkData $data): BukuAgendaSk;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): BukuAgendaSk;

    public function update(BukuAgendaSk $bukuAgendaSk, BukuAgendaSkData $data): BukuAgendaSk;

    public function delete(BukuAgendaSk $bukuAgendaSk): void;
}
