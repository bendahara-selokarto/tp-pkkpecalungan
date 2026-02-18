<?php

namespace App\Policies;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Models\User;

class AnggotaPokjaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function view(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if ($user->hasRole('admin-desa')) {
            return $anggotaPokja->level === 'desa' && (int) $anggotaPokja->area_id === (int) $user->area_id;
        }

        if ($user->hasRole('admin-kecamatan')) {
            return $anggotaPokja->level === 'kecamatan' && (int) $anggotaPokja->area_id === (int) $user->area_id;
        }

        return false;
    }

    public function update(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->view($user, $anggotaPokja);
    }

    public function delete(User $user, AnggotaPokja $anggotaPokja): bool
    {
        return $this->view($user, $anggotaPokja);
    }
}
