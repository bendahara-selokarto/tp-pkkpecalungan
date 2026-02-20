<?php

namespace App\Support;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;

class RoleScopeMatrix
{
    /**
     * @return array<string, list<string>>
     */
    public static function scopedRoles(): array
    {
        return [
            ScopeLevel::DESA->value => [
                'desa-sekretaris',
                'desa-bendahara',
                'desa-pokja-i',
                'desa-pokja-ii',
                'desa-pokja-iii',
                'desa-pokja-iv',
                // Backward compatibility.
                'admin-desa',
            ],
            ScopeLevel::KECAMATAN->value => [
                'kecamatan-sekretaris',
                'kecamatan-bendahara',
                'kecamatan-pokja-i',
                'kecamatan-pokja-ii',
                'kecamatan-pokja-iii',
                'kecamatan-pokja-iv',
                // Backward compatibility.
                'admin-kecamatan',
                'super-admin',
            ],
        ];
    }

    public static function isRoleCompatibleWithScope(string $role, string $scope): bool
    {
        return in_array($role, self::scopedRoles()[$scope] ?? [], true);
    }

    public static function userHasRoleForScope(User $user, string $scope): bool
    {
        return $user->hasAnyRole(self::scopedRoles()[$scope] ?? []);
    }

    /**
     * @return list<string>
     */
    public static function assignableRolesForScope(string $scope): array
    {
        $roles = self::scopedRoles()[$scope] ?? [];

        // Legacy admin roles are accepted but hidden from new assignment UI.
        return array_values(array_filter(
            $roles,
            static fn (string $role) => ! in_array($role, ['admin-desa', 'admin-kecamatan'], true)
        ));
    }
}
