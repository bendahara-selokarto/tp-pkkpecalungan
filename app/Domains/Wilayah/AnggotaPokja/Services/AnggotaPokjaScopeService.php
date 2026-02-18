<?php

namespace App\Domains\Wilayah\AnggotaPokja\Services;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaPokjaScopeService
{
    public function canAccessLevel(User $user, string $level): bool
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if ($level === 'desa') {
            return $user->hasRoleForScope('desa') && $areaLevel === 'desa';
        }

        if ($level === 'kecamatan') {
            return $user->hasRoleForScope('kecamatan') && $areaLevel === 'kecamatan';
        }

        return false;
    }

    public function canEnterModule(User $user): bool
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if (! is_string($areaLevel)) {
            return false;
        }

        return $this->canAccessLevel($user, $areaLevel);
    }

    public function canView(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! $this->canAccessLevel($user, $anggotaPokja->level)) {
            return false;
        }

        return (int) $anggotaPokja->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->canView($user, $anggotaPokja);
    }

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

    private function resolveUserAreaLevel(User $user): ?string
    {
        if (! is_numeric($user->area_id)) {
            return null;
        }

        $loadedLevel = $user->relationLoaded('area') ? $user->area?->level : null;
        if (is_string($loadedLevel)) {
            return $loadedLevel;
        }

        return Area::query()
            ->whereKey((int) $user->area_id)
            ->value('level');
    }
}
