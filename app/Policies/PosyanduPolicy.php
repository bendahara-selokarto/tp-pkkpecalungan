<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Services\PosyanduScopeService;
use App\Models\User;

class PosyanduPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function __construct(
        private readonly PosyanduScopeService $posyanduScopeService
    ) {
    }

    public function viewAny(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'posyandu.view');
    }

    public function create(User $user): bool
    {
        return RoleScopeMatrix::userHasPermission($user, 'posyandu.create');
    }

    public function view(User $user, Posyandu $posyandu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'posyandu.view')) {
            return false;
        }

        return $this->posyanduScopeService->canView($user, $posyandu);
    }

    public function update(User $user, Posyandu $posyandu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'posyandu.update')) {
            return false;
        }

        return $this->posyanduScopeService->canUpdate($user, $posyandu);
    }

    public function delete(User $user, Posyandu $posyandu): bool
    {
        if (! RoleScopeMatrix::userHasPermission($user, 'posyandu.delete')) {
            return false;
        }

        return $this->view($user, $posyandu);
    }
}


