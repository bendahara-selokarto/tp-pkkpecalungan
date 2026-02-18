<?php
namespace App\Actions\User;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateUserAction
{
    public function execute(array $data): User
    {
        $this->assertValidRoleScopeArea($data);

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'scope'    => $data['scope'] ?? 'desa',
                'area_id'  => $data['area_id'] ?? null,
            ]);

            $user->syncRoles([$data['role']]);

            return $user;
        });
    }

    private function assertValidRoleScopeArea(array $data): void
    {
        $scope = (string) ($data['scope'] ?? '');
        $role = (string) ($data['role'] ?? '');
        $areaId = isset($data['area_id']) ? (int) $data['area_id'] : 0;

        $area = Area::find($areaId);
        if (! $area || $area->level !== $scope) {
            throw ValidationException::withMessages([
                'area_id' => 'Area tidak sesuai dengan scope yang dipilih.',
            ]);
        }

        $roleValid = ($scope === 'desa' && $role === 'admin-desa')
            || ($scope === 'kecamatan' && in_array($role, ['admin-kecamatan', 'super-admin'], true));

        if (! $roleValid) {
            throw ValidationException::withMessages([
                'role' => 'Role tidak sesuai dengan scope yang dipilih.',
            ]);
        }
    }
}
