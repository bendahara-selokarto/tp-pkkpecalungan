<?php

namespace App\Domains\Wilayah\DataKeluarga\Repositories;

use App\Domains\Wilayah\DataKeluarga\DTOs\DataKeluargaData;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use Illuminate\Support\Collection;

interface DataKeluargaRepositoryInterface
{
    public function store(DataKeluargaData $data): DataKeluarga;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataKeluarga;

    public function update(DataKeluarga $dataKeluarga, DataKeluargaData $data): DataKeluarga;

    public function delete(DataKeluarga $dataKeluarga): void;
}

