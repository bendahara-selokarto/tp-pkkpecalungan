<?php

namespace App\Policies;

use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class BukuNotulenSimulasiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.view');
    }

    public function view(User $user, BukuNotulenSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.view')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.create');
    }

    public function update(User $user, BukuNotulenSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.update')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function delete(User $user, BukuNotulenSimulasi $model): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.delete')) {
            return false;
        }

        return (int) $model->area_id === (int) $user->area_id;
    }

    public function print(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'buku_notulen_simulasi.print');
    }
}
