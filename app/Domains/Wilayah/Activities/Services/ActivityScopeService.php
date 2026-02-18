<?php

namespace App\Domains\Wilayah\Activities\Services;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActivityScopeService
{
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
        if ($user->hasRole('admin-desa')) {
            return $this->isSameLevelAndArea($activity, 'desa', (int) $user->area_id);
        }

        if ($user->hasRole('admin-kecamatan')) {
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
        if ($user->hasRole('admin-desa')) {
            return $this->isSameLevelAndArea($activity, 'desa', (int) $user->area_id);
        }

        if ($user->hasRole('admin-kecamatan')) {
            return $this->isSameLevelAndArea($activity, 'kecamatan', (int) $user->area_id);
        }

        return false;
    }

    public function canPrint(User $user, Activity $activity): bool
    {
        if ($user->hasRole('admin-desa') && $user->scope !== 'desa') {
            return false;
        }

        if ($user->hasRole('admin-kecamatan') && $user->scope !== 'kecamatan') {
            return false;
        }

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
}
