<?php

namespace App\Policies;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Models\User;

class BantuanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin-desa', 'admin-kecamatan', 'super-admin']);
    }

    public function view(User $user, Bantuan $bantuan): bool
    {
        if ($user->hasRole('admin-desa')) {
            return $bantuan->level === 'desa' && (int) $bantuan->area_id === (int) $user->area_id;
        }

        if ($user->hasRole('admin-kecamatan')) {
            return $bantuan->level === 'kecamatan' && (int) $bantuan->area_id === (int) $user->area_id;
        }

        return false;
    }

    public function update(User $user, Bantuan $bantuan): bool
    {
        return $this->view($user, $bantuan);
    }

    public function delete(User $user, Bantuan $bantuan): bool
    {
        return $this->view($user, $bantuan);
    }
}
