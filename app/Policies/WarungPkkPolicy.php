<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Services\WarungPkkScopeService;
use App\Models\User;

class WarungPkkPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly WarungPkkScopeService $warungPkkScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'warung_pkk.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'warung_pkk.create');
    }

    public function view(User $user, WarungPkk $warungPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'warung_pkk.view')) {
            return false;
        }

        return $this->warungPkkScopeService->canView($user, $warungPkk);
    }

    public function update(User $user, WarungPkk $warungPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'warung_pkk.update')) {
            return false;
        }

        return $this->warungPkkScopeService->canUpdate($user, $warungPkk);
    }

    public function delete(User $user, WarungPkk $warungPkk): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'warung_pkk.delete')) {
            return false;
        }

        return $this->view($user, $warungPkk);
    }
}
