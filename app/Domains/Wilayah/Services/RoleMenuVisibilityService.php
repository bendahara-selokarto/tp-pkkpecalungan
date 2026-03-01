<?php

namespace App\Domains\Wilayah\Services;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Models\User;

class RoleMenuVisibilityService
{
    public const PILOT_MODULE_SLUG = 'catatan-keluarga';

    public const MODE_READ_ONLY = 'read-only';

    public const MODE_READ_WRITE = 'read-write';

    public const MODE_HIDDEN = 'hidden';

    /**
     * @var array<string, list<string>>
     */
    private const GROUP_MODULES = [
        'sekretaris-tpk' => [
            'anggota-tim-penggerak',
            'anggota-tim-penggerak-kader',
            'kader-khusus',
            'agenda-surat',
            'buku-daftar-hadir',
            'buku-tamu',
            'buku-notulen-rapat',
            'buku-keuangan',
            'bantuans',
            'inventaris',
            'activities',
            'program-prioritas',
            'anggota-pokja',
            'prestasi-lomba',
            'laporan-tahunan-pkk',
        ],
        'pokja-i' => [
            'activities',
            'anggota-pokja',
            'prestasi-lomba',
            'data-warga',
            'data-kegiatan-warga',
            'bkl',
            'bkr',
            'paar',
        ],
        'pokja-ii' => [
            'activities',
            'anggota-pokja',
            'prestasi-lomba',
            'data-pelatihan-kader',
            'taman-bacaan',
            'koperasi',
            'kejar-paket',
        ],
        'pokja-iii' => [
            'activities',
            'anggota-pokja',
            'prestasi-lomba',
            'data-keluarga',
            'data-industri-rumah-tangga',
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'warung-pkk',
        ],
        'pokja-iv' => [
            'activities',
            'anggota-pokja',
            'prestasi-lomba',
            'posyandu',
            'simulasi-penyuluhan',
            'catatan-keluarga',
            'pilot-project-naskah-pelaporan',
            'pilot-project-keluarga-sehat',
        ],
        'monitoring' => [
            'desa-activities',
            'desa-arsip',
        ],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const GROUPS_BY_SCOPE = [
        'desa' => [
            'sekretaris-tpk',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
        ],
        'kecamatan' => [
            'sekretaris-tpk',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
            'monitoring',
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const ROLE_GROUP_MODES = [
        'desa-sekretaris' => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_ONLY,
            'pokja-ii' => self::MODE_READ_ONLY,
            'pokja-iii' => self::MODE_READ_ONLY,
            'pokja-iv' => self::MODE_READ_ONLY,
        ],
        'kecamatan-sekretaris' => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_ONLY,
            'pokja-ii' => self::MODE_READ_ONLY,
            'pokja-iii' => self::MODE_READ_ONLY,
            'pokja-iv' => self::MODE_READ_ONLY,
            'monitoring' => self::MODE_READ_ONLY,
        ],
        'desa-pokja-i' => [
            'pokja-i' => self::MODE_READ_WRITE,
        ],
        'desa-pokja-ii' => [
            'pokja-ii' => self::MODE_READ_WRITE,
        ],
        'desa-pokja-iii' => [
            'pokja-iii' => self::MODE_READ_WRITE,
        ],
        'desa-pokja-iv' => [
            'pokja-iv' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-i' => [
            'pokja-i' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-ii' => [
            'pokja-ii' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iii' => [
            'pokja-iii' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iv' => [
            'pokja-iv' => self::MODE_READ_WRITE,
        ],
        // Backward compatibility while legacy roles are migrated.
        'admin-desa' => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_WRITE,
            'pokja-ii' => self::MODE_READ_WRITE,
            'pokja-iii' => self::MODE_READ_WRITE,
            'pokja-iv' => self::MODE_READ_WRITE,
        ],
        'admin-kecamatan' => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_WRITE,
            'pokja-ii' => self::MODE_READ_WRITE,
            'pokja-iii' => self::MODE_READ_WRITE,
            'pokja-iv' => self::MODE_READ_WRITE,
            'monitoring' => self::MODE_READ_ONLY,
        ],
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private const ROLE_MODULE_MODE_OVERRIDES = [
        // Modul pokja tertentu diturunkan menjadi read-only untuk role kecamatan pokja.
        'kecamatan-pokja-i' => [
            'data-warga' => null,
            'data-kegiatan-warga' => null,
            'bkl' => null,
            'bkr' => null,
            'paar' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-ii' => [
            'data-pelatihan-kader' => null,
            'taman-bacaan' => null,
            'koperasi' => null,
            'kejar-paket' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iii' => [
            'data-keluarga' => null,
            'data-industri-rumah-tangga' => null,
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => null,
            'warung-pkk' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iv' => [
            'posyandu' => null,
            'simulasi-penyuluhan' => null,
            'catatan-keluarga' => null,
            'pilot-project-naskah-pelaporan' => null,
            'pilot-project-keluarga-sehat' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
    ];

    public function __construct(
        private readonly ModuleAccessOverrideRepositoryInterface $moduleAccessOverrideRepository
    ) {
    }

    /**
     * @return array{groups: array<string, string>, modules: array<string, string>}
     */
    public function resolveForScope(User $user, string $scope): array
    {
        if (! $this->userCanResolveScope($user, $scope)) {
            return $this->emptyVisibility();
        }

        $roleNames = $this->roleNamesFromUser($user);
        $groupModes = $this->resolveGroupModesForRoles($roleNames, $scope);
        if ($groupModes === []) {
            return $this->emptyVisibility();
        }

        $moduleModes = $this->resolveModuleModes($groupModes);
        $moduleModes = $this->applyRoleModuleModeOverridesForRoles($roleNames, $scope, $moduleModes);

        return $this->visibilityPayload($groupModes, $moduleModes);
    }

    public function resolveModuleModeForScope(User $user, string $scope, string $moduleSlug): ?string
    {
        $visibility = $this->resolveForScope($user, $scope);

        return $visibility['modules'][$moduleSlug] ?? null;
    }

    /**
     * @return list<string>
     */
    public function groupsForScope(string $scope): array
    {
        return self::GROUPS_BY_SCOPE[$scope] ?? [];
    }

    /**
     * @return array{groups: array<string, string>, modules: array<string, string>}
     */
    public function resolveForRoleScope(string $role, string $scope): array
    {
        $groupModes = $this->resolveGroupModesForRoles([$role], $scope);
        if ($groupModes === []) {
            return $this->emptyVisibility();
        }

        $moduleModes = $this->resolveModuleModes($groupModes);
        $moduleModes = $this->applyRoleModuleModeOverridesMap(
            $this->roleModuleModeOverrides($role, $scope),
            $moduleModes
        );

        return $this->visibilityPayload($groupModes, $moduleModes);
    }

    /**
     * @return array<string, string|null>
     */
    public function roleModuleModeOverrides(string $role, ?string $scope = null): array
    {
        $overrides = self::ROLE_MODULE_MODE_OVERRIDES[$role] ?? [];

        if (! is_string($scope)) {
            return $overrides;
        }

        return $this->appendPilotOverride($overrides, $scope, $role);
    }

    public function resolveModuleModeForRoleScope(string $role, string $scope, string $moduleSlug): ?string
    {
        $visibility = $this->resolveForRoleScope($role, $scope);

        return $visibility['modules'][$moduleSlug] ?? null;
    }

    public function resolveBaselineModuleModeForRoleScope(string $role, string $scope, string $moduleSlug): ?string
    {
        $groupModes = $this->resolveGroupModesForRoles([$role], $scope);
        if ($groupModes === []) {
            return null;
        }

        $moduleModes = $this->resolveModuleModes($groupModes);
        $moduleModes = $this->applyRoleModuleModeOverridesMap(self::ROLE_MODULE_MODE_OVERRIDES[$role] ?? [], $moduleModes);

        return $moduleModes[$moduleSlug] ?? null;
    }

    /**
     * @return list<string>
     */
    public function modulesForGroup(string $group): array
    {
        return self::GROUP_MODULES[$group] ?? [];
    }

    private function userCanResolveScope(User $user, string $scope): bool
    {
        return $user->hasRole('super-admin') || $user->hasRoleForScope($scope);
    }

    /**
     * @return list<string>
     */
    private function roleNamesFromUser(User $user): array
    {
        return $user->getRoleNames()
            ->map(static fn (string $roleName): string => (string) $roleName)
            ->values()
            ->all();
    }

    /**
     * @return array{groups: array<string, string>, modules: array<string, string>}
     */
    private function emptyVisibility(): array
    {
        return $this->visibilityPayload([], []);
    }

    /**
     * @param array<string, string> $groupModes
     * @param array<string, string> $moduleModes
     * @return array{groups: array<string, string>, modules: array<string, string>}
     */
    private function visibilityPayload(array $groupModes, array $moduleModes): array
    {
        return [
            'groups' => $groupModes,
            'modules' => $moduleModes,
        ];
    }

    /**
     * @param list<string> $roleNames
     * @return array<string, string>
     */
    private function resolveGroupModesForRoles(array $roleNames, string $scope): array
    {
        $allowedGroupLookup = $this->allowedGroupLookupForScope($scope);
        if ($allowedGroupLookup === []) {
            return [];
        }

        $groupModes = [];
        foreach ($roleNames as $roleName) {
            $roleModes = self::ROLE_GROUP_MODES[$roleName] ?? [];
            foreach ($roleModes as $group => $mode) {
                if (! array_key_exists($group, $allowedGroupLookup)) {
                    continue;
                }

                $this->assignMode($groupModes, $group, $mode);
            }
        }

        return $groupModes;
    }

    /**
     * @param array<string, string> $groupModes
     * @return array<string, string>
     */
    private function resolveModuleModes(array $groupModes): array
    {
        $moduleModes = [];

        foreach ($groupModes as $group => $mode) {
            foreach (self::GROUP_MODULES[$group] ?? [] as $slug) {
                $this->assignMode($moduleModes, $slug, $mode);
            }
        }

        return $moduleModes;
    }

    /**
     * @param array<string, string> $modes
     */
    private function assignMode(array &$modes, string $key, string $mode): void
    {
        $existing = $modes[$key] ?? null;
        if ($existing === self::MODE_READ_WRITE) {
            return;
        }

        if ($mode === self::MODE_READ_WRITE || $existing === null) {
            $modes[$key] = $mode;
        }
    }

    /**
     * @param array<string, string> $moduleModes
     * @return array<string, string>
     */
    private function applyRoleModuleModeOverridesForRoles(array $roleNames, string $scope, array $moduleModes): array
    {
        $pilotOverrides = $this->pilotOverridesByScopeRoles($scope, $roleNames);

        foreach ($roleNames as $roleName) {
            $overrides = self::ROLE_MODULE_MODE_OVERRIDES[$roleName] ?? [];

            if (array_key_exists($roleName, $pilotOverrides)) {
                $overrides[self::PILOT_MODULE_SLUG] = $this->normalizeOverrideModeForResolver($pilotOverrides[$roleName]);
            }

            $moduleModes = $this->applyRoleModuleModeOverridesMap($overrides, $moduleModes);
        }

        return $moduleModes;
    }

    /**
     * @param list<string> $roleNames
     * @return array<string, string>
     */
    private function pilotOverridesByScopeRoles(string $scope, array $roleNames): array
    {
        if ($roleNames === [] || ! $this->isPilotOverrideEnabled()) {
            return [];
        }

        return $this->moduleAccessOverrideRepository->listModesForScopeRolesAndModule(
            $scope,
            $roleNames,
            self::PILOT_MODULE_SLUG
        );
    }

    /**
     * @return array<string, true>
     */
    private function allowedGroupLookupForScope(string $scope): array
    {
        $allowedGroups = self::GROUPS_BY_SCOPE[$scope] ?? [];
        if ($allowedGroups === []) {
            return [];
        }

        return array_fill_keys($allowedGroups, true);
    }

    /**
     * @param array<string, string|null> $overrides
     * @param array<string, string> $moduleModes
     * @return array<string, string>
     */
    private function applyRoleModuleModeOverridesMap(array $overrides, array $moduleModes): array
    {
        foreach ($overrides as $moduleSlug => $mode) {
            if ($mode === null) {
                unset($moduleModes[$moduleSlug]);
                continue;
            }

            $moduleModes[$moduleSlug] = $mode;
        }

        return $moduleModes;
    }

    private function normalizeOverrideModeForResolver(string $mode): ?string
    {
        return $mode === self::MODE_HIDDEN ? null : $mode;
    }

    /**
     * @param array<string, string|null> $overrides
     * @return array<string, string|null>
     */
    private function appendPilotOverride(array $overrides, string $scope, string $role): array
    {
        if (! $this->isPilotOverrideEnabled()) {
            return $overrides;
        }

        $pilotMode = $this->moduleAccessOverrideRepository->findMode($scope, $role, self::PILOT_MODULE_SLUG);
        if (! is_string($pilotMode)) {
            return $overrides;
        }

        $overrides[self::PILOT_MODULE_SLUG] = $this->normalizeOverrideModeForResolver($pilotMode);

        return $overrides;
    }

    private function isPilotOverrideEnabled(): bool
    {
        return (bool) config('access_control.pilot_override.enabled', true);
    }
}
