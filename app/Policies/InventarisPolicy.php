<?php

namespace App\Policies;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Models\User;

class InventarisPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function view(User $user, Inventaris $inventaris): bool
    {
        if ($user->hasRole('admin-desa')) {
            return $inventaris->level === 'desa' && (int) $inventaris->area_id === (int) $user->area_id;
        }

        if ($user->hasRole('admin-kecamatan')) {
            return $inventaris->level === 'kecamatan' && (int) $inventaris->area_id === (int) $user->area_id;
        }

        return false;
    }

    public function update(User $user, Inventaris $inventaris): bool
    {
        return $this->view($user, $inventaris);
    }

    public function delete(User $user, Inventaris $inventaris): bool
    {
        return $this->view($user, $inventaris);
    }
}
