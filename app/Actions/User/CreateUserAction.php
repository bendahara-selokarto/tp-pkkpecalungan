<?php
namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'scope'    => $data['scope'],
                'area_id'  => $data['area_id'],
            ]);

            $user->syncRoles([$data['role']]);

            return $user;
        });
    }
}
