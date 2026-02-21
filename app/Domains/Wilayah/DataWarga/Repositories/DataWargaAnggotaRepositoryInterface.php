<?php

namespace App\Domains\Wilayah\DataWarga\Repositories;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;

interface DataWargaAnggotaRepositoryInterface
{
    public function syncForDataWarga(DataWarga $dataWarga, array $anggotaRows, string $level, int $areaId, int $createdBy): void;
}
