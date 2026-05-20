<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Services\PaarScopeService;
use App\Models\User;

class PaarPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PaarScopeService $paarScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'paar.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'paar.create');
    }

    public function view(User $user, Paar $paar): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'paar.view')) {
            return false;
        }

        return $this->paarScopeService->canView($user, $paar);
    }

    public function update(User $user, Paar $paar): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'paar.update')) {
            return false;
        }

        return $this->paarScopeService->canUpdate($user, $paar);
    }

    public function delete(User $user, Paar $paar): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'paar.delete')) {
            return false;
        }

        return $this->view($user, $paar);
    }
}
