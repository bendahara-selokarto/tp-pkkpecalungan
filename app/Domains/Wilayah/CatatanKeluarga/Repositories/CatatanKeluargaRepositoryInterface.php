<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use Illuminate\Support\Collection;

interface CatatanKeluargaRepositoryInterface
{
    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapDasaWismaByLevelAndArea(string $level, int $areaId): Collection;

    public function getRekapPkkRtByLevelAndArea(string $level, int $areaId): Collection;
}
