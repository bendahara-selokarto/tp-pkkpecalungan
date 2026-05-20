<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Services\BkrScopeService;
use App\Models\User;

class BkrPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly BkrScopeService $bkrScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkr.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'bkr.create');
    }

    public function view(User $user, Bkr $bkr): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkr.view')) {
            return false;
        }

        return $this->bkrScopeService->canView($user, $bkr);
    }

    public function update(User $user, Bkr $bkr): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkr.update')) {
            return false;
        }

        return $this->bkrScopeService->canUpdate($user, $bkr);
    }

    public function delete(User $user, Bkr $bkr): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'bkr.delete')) {
            return false;
        }

        return $this->view($user, $bkr);
    }
}


