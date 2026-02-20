<?php

namespace App\Domains\Wilayah\KaderKhusus\Repositories;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use Illuminate\Support\Collection;

interface KaderKhususRepositoryInterface
{
    public function store(KaderKhususData $data): KaderKhusus;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): KaderKhusus;

    public function update(KaderKhusus $kaderKhusus, KaderKhususData $data): KaderKhusus;

    public function delete(KaderKhusus $kaderKhusus): void;
}
