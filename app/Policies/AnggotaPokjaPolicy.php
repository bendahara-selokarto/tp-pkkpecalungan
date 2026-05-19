<?php

namespace App\Policies;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnggotaPokjaPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'anggota_pokja.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'anggota_pokja.create');
    }

    public function view(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_pokja.view')) {
            return false;
        }

        return $this->anggotaPokjaScopeService->canView($user, $anggotaPokja);
    }

    public function update(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_pokja.update')) {
            return false;
        }

        return $this->anggotaPokjaScopeService->canUpdate($user, $anggotaPokja);
    }

    public function delete(User $user, AnggotaPokja $anggotaPokja): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'anggota_pokja.delete')) {
            return false;
        }

        return $this->view($user, $anggotaPokja);
    }
}
