<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\DTOs\PelatihanKaderPokjaIiData;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PelatihanKaderPokjaIiRepositoryInterface
{
    public function store(PelatihanKaderPokjaIiData $data): PelatihanKaderPokjaIi;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function find(int $id): PelatihanKaderPokjaIi;

    public function update(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi, PelatihanKaderPokjaIiData $data): PelatihanKaderPokjaIi;

    public function delete(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): void;
}
