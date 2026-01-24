<?php
namespace App\Actions\User;

use App\Models\User;

class DeleteUserAction
{
    public function execute(User $user): void
    {
        if ($user->hasRole('super-admin')) {
            throw new \Exception('Super Admin tidak boleh dihapus');
        }

        $user->delete();
    }
}
