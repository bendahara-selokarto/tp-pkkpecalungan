<?php
namespace App\Actions\User;

use App\Models\User;
use DomainException;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        if ($user->hasRole('super-admin')) {
            throw new DomainException('Super Admin tidak boleh dihapus');
        }

        $user->delete();
    }
}
