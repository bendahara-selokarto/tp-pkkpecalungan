<?php

namespace App\Domains\Wilayah\KejarPaket\Repositories;

use App\Domains\Wilayah\KejarPaket\DTOs\KejarPaketData;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use Illuminate\Support\Collection;

interface KejarPaketRepositoryInterface
{
    public function store(KejarPaketData $data): KejarPaket;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): KejarPaket;

    public function update(KejarPaket $kejarPaket, KejarPaketData $data): KejarPaket;

    public function delete(KejarPaket $kejarPaket): void;
}





