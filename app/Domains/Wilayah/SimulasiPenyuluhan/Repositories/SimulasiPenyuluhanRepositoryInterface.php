<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Repositories;

use App\Domains\Wilayah\SimulasiPenyuluhan\DTOs\SimulasiPenyuluhanData;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use Illuminate\Support\Collection;

interface SimulasiPenyuluhanRepositoryInterface
{
    public function store(SimulasiPenyuluhanData $data): SimulasiPenyuluhan;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): SimulasiPenyuluhan;

    public function update(SimulasiPenyuluhan $simulasiPenyuluhan, SimulasiPenyuluhanData $data): SimulasiPenyuluhan;

    public function delete(SimulasiPenyuluhan $simulasiPenyuluhan): void;
}
