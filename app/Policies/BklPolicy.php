<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Services\BklScopeService;
use App\Models\User;

class BklPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BklScopeService $bklScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkl.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkl.create');
    }

    public function view(User $user, Bkl $bkl): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkl.view')) {
            return false;
        }

        return $this->bklScopeService->canView($user, $bkl);
    }

    public function update(User $user, Bkl $bkl): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkl.update')) {
            return false;
        }

        return $this->bklScopeService->canUpdate($user, $bkl);
    }

    public function delete(User $user, Bkl $bkl): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkl.delete')) {
            return false;
        }

        return $this->view($user, $bkl);
    }
}

