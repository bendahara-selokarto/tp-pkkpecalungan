<?php

namespace App\Policies;

use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuDaftarHadirSimulasiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.view');
    }

    public function view(User $user, BukuDaftarHadirSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.view')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.create');
    }

    public function update(User $user, BukuDaftarHadirSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.update')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function delete(User $user, BukuDaftarHadirSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.delete')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function print(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_daftar_hadir_simulasi.print');
    }
}
