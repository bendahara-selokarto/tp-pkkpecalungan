<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Repositories;

use Illuminate\Support\Collection;

interface CatatanKeluargaRepositoryInterface
{
    public function getByLevelAndArea(string $level, int $areaId): Collection;
}

