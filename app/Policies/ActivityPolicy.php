<?php

namespace App\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function view(User $user, Activity $activity): bool
    {
        if ($user->hasRole('admin-desa')) {
            return $activity->level === 'desa' && (int) $activity->area_id === (int) $user->area_id;
        }

        if ($user->hasRole('admin-kecamatan')) {
            if ($activity->level === 'kecamatan') {
                return (int) $activity->area_id === (int) $user->area_id;
            }

            if ($activity->level === 'desa') {
                return (int) $activity->area?->parent_id === (int) $user->area_id;
            }
        }

        return false;
    }

    public function update(User $user, Activity $activity): bool
    {
        if ($user->hasRole('admin-desa')) {
            return $activity->level === 'desa' && (int) $activity->area_id === (int) $user->area_id;
        }

        if ($user->hasRole('admin-kecamatan')) {
            return $activity->level === 'kecamatan' && (int) $activity->area_id === (int) $user->area_id;
        }

        return false;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }
}
