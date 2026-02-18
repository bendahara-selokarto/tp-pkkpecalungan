<?php

namespace App\Domains\Wilayah\Inventaris\Services;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InventarisScopeService
{
    public function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }

    public function authorizeSameLevelAndArea(Inventaris $inventaris, string $level, int $areaId): Inventaris
    {
        if ($inventaris->level !== $level || (int) $inventaris->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $inventaris;
    }
}
