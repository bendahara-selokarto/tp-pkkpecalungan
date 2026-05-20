<?php

namespace App\Actions\User;

use App\Models\User;
use DomainException;

use App\Support\RoleScopeMatrix;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        if ($user->hasRole(RoleScopeMatrix::ROLE_SUPER_ADMIN)) {
            throw new DomainException('Super Admin tidak boleh dihapus');
        }

        $user->delete();
    }
}
