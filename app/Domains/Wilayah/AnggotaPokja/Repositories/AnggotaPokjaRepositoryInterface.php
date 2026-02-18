<?php

namespace App\Domains\Wilayah\AnggotaPokja\Repositories;

use App\Domains\Wilayah\AnggotaPokja\DTOs\AnggotaPokjaData;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use Illuminate\Support\Collection;

interface AnggotaPokjaRepositoryInterface
{
    public function store(AnggotaPokjaData $data): AnggotaPokja;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): AnggotaPokja;

    public function update(AnggotaPokja $anggotaPokja, AnggotaPokjaData $data): AnggotaPokja;

    public function delete(AnggotaPokja $anggotaPokja): void;
}


