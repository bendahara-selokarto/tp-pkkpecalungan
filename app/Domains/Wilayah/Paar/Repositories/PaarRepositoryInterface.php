<?php

namespace App\Domains\Wilayah\Paar\Repositories;

use App\Domains\Wilayah\Paar\DTOs\PaarData;
use App\Domains\Wilayah\Paar\Models\Paar;
use Illuminate\Support\Collection;

interface PaarRepositoryInterface
{
    public function store(PaarData $data): Paar;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Paar;

    public function update(Paar $paar, PaarData $data): Paar;

    public function delete(Paar $paar): void;
}