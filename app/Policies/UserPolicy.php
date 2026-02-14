<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function create(User $authUser): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole('super-admin');
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole('super-admin');
    }
}
