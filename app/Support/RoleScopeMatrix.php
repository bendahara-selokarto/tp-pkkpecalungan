<?php

namespace App\Support;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Models\User;

/**
 * RoleScopeMatrix defines the hierarchical permissions and scopes
 * based on the RAKERNAS X PKK organizational structure.
 */
class RoleScopeMatrix
{
    // Administrative Roles
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_PUSAT = 'admin_pusat';
    public const ROLE_ADMIN_PROVINSI = 'admin_provinsi';
    public const ROLE_ADMIN_KABUPATEN = 'admin_kabupaten';
    public const ROLE_ADMIN_KECAMATAN = 'admin_kecamatan';
    public const ROLE_ADMIN_DESA = 'admin_desa';
    public const ROLE_ADMIN_DUSUN = 'admin_dusun';
    public const ROLE_ADMIN_RW = 'admin_rw';
    public const ROLE_ADMIN_RT = 'admin_rt';
    public const ROLE_ADMIN_DASAWISMA = 'admin_dasawisma';

    // Functional Roles (Implementation)
    public const ROLE_SEKRETARIS_KECAMATAN = 'sekretaris_kecamatan';
    public const ROLE_POKJA_1_KECAMATAN = 'pokja_1_kecamatan';
    public const ROLE_POKJA_2_KECAMATAN = 'pokja_2_kecamatan';
    public const ROLE_POKJA_3_KECAMATAN = 'pokja_3_kecamatan';
    public const ROLE_POKJA_4_KECAMATAN = 'pokja_4_kecamatan';

    public const ROLE_SEKRETARIS_DESA = 'sekretaris_desa';
    public const ROLE_POKJA_1_DESA = 'pokja_1_desa';
    public const ROLE_POKJA_2_DESA = 'pokja_2_desa';
    public const ROLE_POKJA_3_DESA = 'pokja_3_desa';
    public const ROLE_POKJA_4_DESA = 'pokja_4_desa';

    /**
     * Permission Matrix mapping roles to their capabilities.
     * Permissions are structured as: [domain].[action]
     */
    private const PERMISSIONS = [
        self::ROLE_SUPER_ADMIN => ['*'],

        self::ROLE_ADMIN_PUSAT => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.print',
        ],

        self::ROLE_ADMIN_KECAMATAN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_SEKRETARIS_KECAMATAN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_POKJA_1_KECAMATAN => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_2_KECAMATAN => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_3_KECAMATAN => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_4_KECAMATAN => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],

        self::ROLE_ADMIN_DESA => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_SEKRETARIS_DESA => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_POKJA_1_DESA => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_2_DESA => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_3_DESA => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],
        self::ROLE_POKJA_4_DESA => ['activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print'],

        self::ROLE_ADMIN_DUSUN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],
    ];

    /**
     * Check if a role has a specific permission.
     */
    public static function hasPermission(?string $role, string $permission): bool
    {
        if (!$role || !isset(self::PERMISSIONS[$role])) {
            return false;
        }

        $rolePermissions = self::PERMISSIONS[$role];

        if (in_array('*', $rolePermissions, true)) {
            return true;
        }

        return in_array($permission, $rolePermissions, true);
    }

    /**
     * Map role to functional job group (e.g., pokja-i, sekretaris-tpk).
     */
    public static function resolveJobGroup(string $role): ?string
    {
        return match ($role) {
            self::ROLE_SEKRETARIS_DESA, self::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
            self::ROLE_POKJA_1_DESA, self::ROLE_POKJA_1_KECAMATAN => 'pokja-i',
            self::ROLE_POKJA_2_DESA, self::ROLE_POKJA_2_KECAMATAN => 'pokja-ii',
            self::ROLE_POKJA_3_DESA, self::ROLE_POKJA_3_KECAMATAN => 'pokja-iii',
            self::ROLE_POKJA_4_DESA, self::ROLE_POKJA_4_KECAMATAN => 'pokja-iv',
            default => null,
        };
    }

    /**
     * @return array<string, list<string>>
     */
    public static function scopedRoles(): array
    {
        return [
            ScopeLevel::DESA->value => [
                self::ROLE_ADMIN_DESA,
                self::ROLE_SEKRETARIS_DESA,
                self::ROLE_POKJA_1_DESA,
                self::ROLE_POKJA_2_DESA,
                self::ROLE_POKJA_3_DESA,
                self::ROLE_POKJA_4_DESA,
                self::ROLE_ADMIN_DUSUN,
                self::ROLE_ADMIN_RW,
                self::ROLE_ADMIN_RT,
                self::ROLE_ADMIN_DASAWISMA,
            ],
            ScopeLevel::KECAMATAN->value => [
                self::ROLE_ADMIN_KECAMATAN,
                self::ROLE_SEKRETARIS_KECAMATAN,
                self::ROLE_POKJA_1_KECAMATAN,
                self::ROLE_POKJA_2_KECAMATAN,
                self::ROLE_POKJA_3_KECAMATAN,
                self::ROLE_POKJA_4_KECAMATAN,
                self::ROLE_SUPER_ADMIN,
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

        return array_values(array_filter(
            $roles,
            static fn (string $role) => $role !== self::ROLE_SUPER_ADMIN
        ));
    }

    public static function isRestrictedForManagedAssignment(string $role): bool
    {
        return $role === self::ROLE_SUPER_ADMIN;
    }
}
