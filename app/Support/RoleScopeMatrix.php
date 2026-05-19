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
    public const ROLE_SUPER_ADMIN = 'super-admin';
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
    public const ROLE_SEKRETARIS_KECAMATAN = 'kecamatan-sekretaris';
    public const ROLE_BENDAHARA_KECAMATAN = 'kecamatan-bendahara';
    public const ROLE_POKJA_1_KECAMATAN = 'kecamatan-pokja-i';
    public const ROLE_POKJA_2_KECAMATAN = 'kecamatan-pokja-ii';
    public const ROLE_POKJA_3_KECAMATAN = 'kecamatan-pokja-iii';
    public const ROLE_POKJA_4_KECAMATAN = 'kecamatan-pokja-iv';

    public const ROLE_SEKRETARIS_DESA = 'desa-sekretaris';
    public const ROLE_BENDAHARA_DESA = 'desa-bendahara';
    public const ROLE_POKJA_1_DESA = 'desa-pokja-i';
    public const ROLE_POKJA_2_DESA = 'desa-pokja-ii';
    public const ROLE_POKJA_3_DESA = 'desa-pokja-iii';
    public const ROLE_POKJA_4_DESA = 'desa-pokja-iv';

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
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
        ],

        self::ROLE_SEKRETARIS_KECAMATAN => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
        ],

        self::ROLE_BENDAHARA_KECAMATAN => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_POKJA_1_KECAMATAN => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_2_KECAMATAN => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_3_KECAMATAN => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_4_KECAMATAN => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_ADMIN_DESA => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
        ],

        self::ROLE_SEKRETARIS_DESA => [
            'arsip_document.view', 'arsip_document.create', 'arsip_document.update', 'arsip_document.delete', 'arsip_document.export',
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'agenda_surat.view', 'agenda_surat.create', 'agenda_surat.update', 'agenda_surat.delete', 'agenda_surat.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
        ],

        self::ROLE_BENDAHARA_DESA => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
        ],

        self::ROLE_POKJA_1_DESA => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'anggota_pokja.view', 'anggota_pokja.create', 'anggota_pokja.update', 'anggota_pokja.delete', 'anggota_pokja.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_2_DESA => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_3_DESA => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

        self::ROLE_POKJA_4_DESA => [
            'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.print',
            'program_prioritas.view', 'program_prioritas.create', 'program_prioritas.update', 'program_prioritas.delete', 'program_prioritas.print',
            'inventaris.view', 'inventaris.create', 'inventaris.update', 'inventaris.delete', 'inventaris.print',
            'bantuan.view', 'bantuan.create', 'bantuan.update', 'bantuan.delete', 'bantuan.print',
            'kader_khusus.view', 'kader_khusus.create', 'kader_khusus.update', 'kader_khusus.delete', 'kader_khusus.print',
            'prestasi_lomba.view', 'prestasi_lomba.create', 'prestasi_lomba.update', 'prestasi_lomba.delete', 'prestasi_lomba.print',
        ],

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
     * @return list<string>
     */
    public static function roleNamesForUser(User $user): array
    {
        $attributes = $user->getAttributes();
        $attributeRole = $attributes['role'] ?? null;
        if (is_string($attributeRole) && $attributeRole !== '') {
            return [$attributeRole];
        }

        $roleNames = $user->getRoleNames()->values()->all();

        if ($roleNames !== []) {
            return $roleNames;
        }

        return [];
    }

    public static function primaryRoleForUser(User $user): ?string
    {
        return self::roleNamesForUser($user)[0] ?? null;
    }

    public static function userHasPermission(User $user, string $permission): bool
    {
        foreach (self::roleNamesForUser($user) as $roleName) {
            if (self::hasPermission($roleName, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function userIsSuperAdmin(User $user): bool
    {
        return in_array(self::ROLE_SUPER_ADMIN, self::roleNamesForUser($user), true);
    }

    /**
     * Map role to functional job group (e.g., pokja-i, sekretaris-tpk).
     */
    public static function resolveJobGroup(string $role): ?string
    {
        return match ($role) {
            self::ROLE_SEKRETARIS_DESA, self::ROLE_SEKRETARIS_KECAMATAN => 'sekretaris-tpk',
            self::ROLE_BENDAHARA_DESA, self::ROLE_BENDAHARA_KECAMATAN => 'bendahara-tpk',
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
                self::ROLE_BENDAHARA_DESA,
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
                self::ROLE_BENDAHARA_KECAMATAN,
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
        $scopedRoles = self::scopedRoles()[$scope] ?? [];

        foreach (self::roleNamesForUser($user) as $roleName) {
            if (in_array($roleName, $scopedRoles, true)) {
                return true;
            }
        }

        return false;
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
