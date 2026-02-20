<?php

namespace App\Domains\Wilayah\Posyandu\Repositories;

use App\Domains\Wilayah\Posyandu\DTOs\PosyanduData;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use Illuminate\Support\Collection;

interface PosyanduRepositoryInterface
{
    public function store(PosyanduData $data): Posyandu;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Posyandu;

    public function update(Posyandu $posyandu, PosyanduData $data): Posyandu;

    public function delete(Posyandu $posyandu): void;
}





