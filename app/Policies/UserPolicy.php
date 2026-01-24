<?php

namespace App\Policies;

class UserPolicy
{
    /**
     * Create a new class instance.
     */
    public function manage(User $authUser)
    {
        return $authUser->hasRole('super-admin');
    }
}
