<?php
namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password'])
            ]);
        }

        $user->syncRoles([$data['role']]);

        return $user;
    }
}
