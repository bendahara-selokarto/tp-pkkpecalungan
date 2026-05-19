<?php

namespace App\Policies;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventarisPolicy
{
    use HandlesAuthorization;

    public function __construct(
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'inventaris.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'inventaris.create');
    }

    public function view(User $user, Inventaris $inventaris): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'inventaris.view')) {
            return false;
        }

        return $this->inventarisScopeService->canView($user, $inventaris);
    }

    public function update(User $user, Inventaris $inventaris): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'inventaris.update')) {
            return false;
        }

        return $this->inventarisScopeService->canUpdate($user, $inventaris);
    }

    public function delete(User $user, Inventaris $inventaris): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'inventaris.delete')) {
            return false;
        }

        return $this->view($user, $inventaris);
    }

    public function print(User $user, Inventaris $inventaris): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'inventaris.print')) {
            return false;
        }

        return $this->view($user, $inventaris);
    }
}
