<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Repositories;

use App\Domains\Wilayah\DataIndustriRumahTangga\DTOs\DataIndustriRumahTanggaData;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use Illuminate\Support\Collection;

interface DataIndustriRumahTanggaRepositoryInterface
{
    public function store(DataIndustriRumahTanggaData $data): DataIndustriRumahTangga;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataIndustriRumahTangga;

    public function update(DataIndustriRumahTangga $dataIndustriRumahTangga, DataIndustriRumahTanggaData $data): DataIndustriRumahTangga;

    public function delete(DataIndustriRumahTangga $dataIndustriRumahTangga): void;
}




