<?php

namespace App\Domains\Wilayah\AnggotaPokja\Services;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaPokjaScopeService
{
    public function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }

    public function authorizeSameLevelAndArea(AnggotaPokja $anggotaPokja, string $level, int $areaId): AnggotaPokja
    {
        if ($anggotaPokja->level !== $level || (int) $anggotaPokja->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaPokja;
    }
}
