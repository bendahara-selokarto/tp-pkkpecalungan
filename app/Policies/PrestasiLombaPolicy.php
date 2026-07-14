<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Services\PrestasiLombaScopeService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrestasiLombaPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly PrestasiLombaScopeService $prestasiLombaScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.view')
            && $this->prestasiLombaScopeService->canUsePrestasiLombaBook($user);
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.create')
            && $this->prestasiLombaScopeService->canUsePrestasiLombaBook($user);
    }

    public function view(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.view')) {
            return false;
        }

        return $this->prestasiLombaScopeService->canView($user, $prestasiLomba);
    }

    public function update(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.update')) {
            return false;
        }

        return $this->prestasiLombaScopeService->canUpdate($user, $prestasiLomba);
    }

    public function delete(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.delete')) {
            return false;
        }

        return $this->view($user, $prestasiLomba);
    }

    public function print(User $user, PrestasiLomba $prestasiLomba): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'prestasi_lomba.print')) {
            return false;
        }

        return $this->view($user, $prestasiLomba);
    }
}
