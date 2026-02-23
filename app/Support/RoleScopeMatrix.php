<?php

namespace App\Support;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;

class RoleScopeMatrix
{
    /**
     * @var list<string>
     */
    private const RESTRICTED_ASSIGNABLE_ROLES = [
        'super-admin',
    ];

    /**
     * @var list<string>
     */
    private const HIDDEN_LEGACY_ASSIGNABLE_ROLES = [
        'admin-desa',
        'admin-kecamatan',
    ];

    /**
     * @return array<string, list<string>>
     */
    public static function scopedRoles(): array
    {
        return [
            ScopeLevel::DESA->value => [
                'desa-sekretaris',
                'desa-pokja-i',
                'desa-pokja-ii',
                'desa-pokja-iii',
                'desa-pokja-iv',
                // Backward compatibility.
                'admin-desa',
            ],
            ScopeLevel::KECAMATAN->value => [
                'kecamatan-sekretaris',
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

        // Legacy admin roles and super-admin are accepted by compatibility checks,
        // but hidden from new assignment UI and blocked in managed assignment flow.
        return array_values(array_filter(
            $roles,
            static fn (string $role) => ! in_array(
                $role,
                array_merge(self::HIDDEN_LEGACY_ASSIGNABLE_ROLES, self::RESTRICTED_ASSIGNABLE_ROLES),
                true
            )
        ));
    }

    public static function isRestrictedForManagedAssignment(string $role): bool
    {
        return in_array($role, self::RESTRICTED_ASSIGNABLE_ROLES, true);
    }
}
