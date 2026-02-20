<?php

namespace App\Domains\Wilayah\Services;

use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAreaContextService
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

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

    public function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }

    public function resolveUserAreaLevel(User $user): ?string
    {
        if (! is_numeric($user->area_id)) {
            return null;
        }

        $loadedLevel = $user->relationLoaded('area') ? $user->area?->level : null;
        if (is_string($loadedLevel)) {
            return $loadedLevel;
        }

        return $this->areaRepository->getLevelById((int) $user->area_id);
    }
}

