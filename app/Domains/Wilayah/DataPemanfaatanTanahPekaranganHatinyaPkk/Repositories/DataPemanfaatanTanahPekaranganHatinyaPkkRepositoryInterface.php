<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\DTOs\DataPemanfaatanTanahPekaranganHatinyaPkkData;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use Illuminate\Support\Collection;

interface DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface
{
    public function store(DataPemanfaatanTanahPekaranganHatinyaPkkData $data): DataPemanfaatanTanahPekaranganHatinyaPkk;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): DataPemanfaatanTanahPekaranganHatinyaPkk;

    public function update(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk, DataPemanfaatanTanahPekaranganHatinyaPkkData $data): DataPemanfaatanTanahPekaranganHatinyaPkk;

    public function delete(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk): void;
}



