<?php

namespace App\Policies;

use App\Support\RoleScopeMatrix;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return RoleScopeMatrix::userIsSuperAdmin($authUser);
    }

    public function view(User $authUser, User $targetUser): bool
    {
        return RoleScopeMatrix::userIsSuperAdmin($authUser);
    }

    public function create(User $authUser): bool
    {
        return RoleScopeMatrix::userIsSuperAdmin($authUser);
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return RoleScopeMatrix::userIsSuperAdmin($authUser);
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return RoleScopeMatrix::userIsSuperAdmin($authUser);
    }
}
