<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityScopeService
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

    public function isSameLevelAndArea(Activity $activity, string $level, int $areaId): bool
    {
        return $activity->level === $level && $activity->area_id === $areaId;
    }

    public function isDesaInKecamatan(Activity $activity, int $kecamatanAreaId): bool
    {
        return $activity->level === 'desa'
            && $activity->area?->level === 'desa'
            && $activity->area?->parent_id === $kecamatanAreaId;
    }

    public function canView(User $user, Activity $activity): bool
    {
        if ($user->hasRoleForScope('desa')) {
            if (! $this->canAccessLevel($user, 'desa')) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, 'desa', (int) $user->area_id);
        }

        if ($user->hasRoleForScope('kecamatan')) {
            if (! $this->canAccessLevel($user, 'kecamatan')) {
                return false;
            }

            if ($activity->level === 'kecamatan') {
                return $this->isSameLevelAndArea($activity, 'kecamatan', (int) $user->area_id);
            }

            if ($activity->level === 'desa') {
                return $this->isDesaInKecamatan($activity, (int) $user->area_id);
            }
        }

        return false;
    }

    public function canUpdate(User $user, Activity $activity): bool
    {
        if ($user->hasRoleForScope('desa')) {
            if (! $this->canAccessLevel($user, 'desa')) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, 'desa', (int) $user->area_id);
        }

        if ($user->hasRoleForScope('kecamatan')) {
            if (! $this->canAccessLevel($user, 'kecamatan')) {
                return false;
            }

            return $this->isSameLevelAndArea($activity, 'kecamatan', (int) $user->area_id);
        }

        return false;
    }

    public function canPrint(User $user, Activity $activity): bool
    {
        return $this->canView($user, $activity);
    }

    public function authorizeSameLevelAndArea(Activity $activity, string $level, int $areaId): Activity
    {
        if (! $this->isSameLevelAndArea($activity, $level, $areaId)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
    }

    public function authorizeDesaInKecamatan(Activity $activity, int $kecamatanAreaId): Activity
    {
        if (! $this->isDesaInKecamatan($activity, $kecamatanAreaId)) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $activity;
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

        return $this->areaRepository->getLevelById((int) $user->area_id);
    }
}
