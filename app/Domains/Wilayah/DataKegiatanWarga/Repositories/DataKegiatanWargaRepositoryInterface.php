<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Repositories;

use App\Domains\Wilayah\DataKegiatanWarga\DTOs\DataKegiatanWargaData;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use Illuminate\Support\Collection;

interface DataKegiatanWargaRepositoryInterface
{
    public function store(DataKegiatanWargaData $data): DataKegiatanWarga;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataKegiatanWarga;

    public function update(DataKegiatanWarga $dataKegiatanWarga, DataKegiatanWargaData $data): DataKegiatanWarga;

    public function delete(DataKegiatanWarga $dataKegiatanWarga): void;
}
