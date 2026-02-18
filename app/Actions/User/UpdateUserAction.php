<?php
namespace App\Actions\User;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        $this->assertValidRoleScopeArea($data);

        $updatePayload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'scope' => $data['scope'] ?? $user->scope,
            'area_id' => $data['area_id'] ?? $user->area_id,
        ];

        $user->update($updatePayload);

        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password'])
            ]);
        }

        $user->syncRoles([$data['role']]);

        return $user;
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
