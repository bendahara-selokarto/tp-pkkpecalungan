<?php

namespace App\Domains\Wilayah\WarungPkk\Repositories;

use App\Domains\Wilayah\WarungPkk\DTOs\WarungPkkData;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use Illuminate\Support\Collection;

interface WarungPkkRepositoryInterface
{
    public function store(WarungPkkData $data): WarungPkk;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): WarungPkk;

    public function update(WarungPkk $warungPkk, WarungPkkData $data): WarungPkk;

    public function delete(WarungPkk $warungPkk): void;
}
