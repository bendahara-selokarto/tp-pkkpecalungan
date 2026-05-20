<?php

namespace App\Domains\Wilayah\Services;

use App\Domains\Wilayah\AccessControl\Repositories\ModuleAccessOverrideRepositoryInterface;
use App\Models\User;
use App\Support\RoleScopeMatrix;

class RoleMenuVisibilityService
{
    public const PILOT_MODULE_SLUG = 'catatan-keluarga';

    public const MODE_READ_ONLY = 'read-only';

    public const MODE_READ_WRITE = 'read-write';

    public const MODE_HIDDEN = 'hidden';

    /**
     * @var list<string>
     */
    private const DEFAULT_ROLLOUT_OVERRIDE_MODULES = [
        self::PILOT_MODULE_SLUG,
        'activities',
        'agenda-surat',
    ];

    /**
     * @var array<string, list<string>>
     */
    private const GROUP_MODULES = [
        'sekretaris-tpk' => [
            'anggota-tim-penggerak',
            'anggota-tim-penggerak-kader',
            'anggota-pokja',
            'agenda-surat',
            'buku-notulen-rapat',
            'inventaris',
            'activities',
            'prestasi-lomba',
            'bantuans',
            'kader-khusus',
        ],
        'penunjang-buku-wajib' => [
            'catatan-keluarga',
            'program-prioritas',
        ],
        'pokja-i' => [
            'program-prioritas',
            'data-kegiatan-pkk-pokja-i',
            'activities',
            'prestasi-lomba',
            'bantuans',
            'inventaris',
            'kader-khusus',
            'simulasi-penyuluhan',
            'bkr',
            'anggota-pokja',
            'paar',
        ],
        'pokja-ii' => [
            'program-prioritas',
            'catatan-keluarga',
            'activities',
            'prestasi-lomba',
            'bantuans',
            'inventaris',
            'kader-khusus',
            'pra-koperasi-up2k',
        ],
        'pokja-iii' => [
            'program-prioritas',
            'catatan-keluarga',
            'activities',
            'prestasi-lomba',
            'bantuans',
            'kader-khusus',
            'data-keluarga',
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'data-industri-rumah-tangga',
            'inventaris',
        ],
        'pokja-iv' => [
            'program-prioritas',
            'catatan-keluarga',
            'activities',
            'prestasi-lomba',
            'bantuans',
            'inventaris',
            'kader-khusus',
            'posyandu',
        ],
        'monitoring' => [
            'desa-activities',
            'desa-arsip',
        ],
        'belum-ada-pemilik' => [
            'buku-daftar-hadir',
            'buku-tamu',
            'buku-keuangan',
            'laporan-tahunan-pkk',
            'data-warga',
            'data-kegiatan-warga',
            'bkl',
            'data-pelatihan-kader',
            'taman-bacaan',
            'koperasi',
            'kejar-paket',
            'literasi-warga',
            'bkb-kegiatan',
            'tutor-khusus',
            'pelatihan-kader-pokja-ii',
            'warung-pkk',
            'pilot-project-naskah-pelaporan',
            'pilot-project-keluarga-sehat',
        ],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const GROUPS_BY_SCOPE = [
        'desa' => [
            'sekretaris-tpk',
            'penunjang-buku-wajib',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
            'belum-ada-pemilik',
        ],
        'kecamatan' => [
            'sekretaris-tpk',
            'penunjang-buku-wajib',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
            'monitoring',
            'belum-ada-pemilik',
        ],
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private const ROLE_GROUP_MODES = [
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'penunjang-buku-wajib' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_ONLY,
            'pokja-ii' => self::MODE_READ_ONLY,
            'pokja-iii' => self::MODE_READ_ONLY,
            'pokja-iv' => self::MODE_READ_ONLY,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'penunjang-buku-wajib' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_ONLY,
            'pokja-ii' => self::MODE_READ_ONLY,
            'pokja-iii' => self::MODE_READ_ONLY,
            'pokja-iv' => self::MODE_READ_ONLY,
            'monitoring' => self::MODE_READ_ONLY,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_1_DESA => [
            'pokja-i' => self::MODE_READ_WRITE,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_2_DESA => [
            'pokja-ii' => self::MODE_READ_WRITE,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_3_DESA => [
            'pokja-iii' => self::MODE_READ_WRITE,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_4_DESA => [
            'pokja-iv' => self::MODE_READ_WRITE,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN => [
            'pokja-i' => self::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN => [
            'pokja-ii' => self::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => [
            'pokja-iii' => self::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN => [
            'pokja-iv' => self::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_SUPER_ADMIN => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'penunjang-buku-wajib' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_WRITE,
            'pokja-ii' => self::MODE_READ_WRITE,
            'pokja-iii' => self::MODE_READ_WRITE,
            'pokja-iv' => self::MODE_READ_WRITE,
            'monitoring' => self::MODE_READ_WRITE,
            'belum-ada-pemilik' => self::MODE_READ_ONLY,
        ],
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private const ROLE_MODULE_MODE_OVERRIDES = [
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

        return $this->appendRolloutOverrides($overrides, $scope, $role);
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

    /**
     * @return list<string>
     */
    public function overrideManageableModules(): array
    {
        $configured = config('access_control.rollout_override.modules', self::DEFAULT_ROLLOUT_OVERRIDE_MODULES);
        if (! is_array($configured)) {
            return [];
        }

        $knownModules = [];
        foreach (self::GROUP_MODULES as $modules) {
            foreach ($modules as $module) {
                $knownModules[$module] = true;
            }
        }

        $manageable = [];
        foreach ($configured as $module) {
            if (! is_string($module)) {
                continue;
            }

            $slug = trim($module);
            if ($slug === '' || ! array_key_exists($slug, $knownModules)) {
                continue;
            }

            $manageable[$slug] = true;
        }

        return array_keys($manageable);
    }

    public function isOverrideManageableModule(string $moduleSlug): bool
    {
        return in_array($moduleSlug, $this->overrideManageableModules(), true);
    }

    public function isModuleAssignableForRoleScope(string $moduleSlug, string $role, string $scope): bool
    {
        $groupModes = $this->resolveGroupModesForRoles([$role], $scope);
        if ($groupModes === []) {
            return false;
        }

        foreach (array_keys($groupModes) as $group) {
            if (in_array($moduleSlug, self::GROUP_MODULES[$group] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    private function userCanResolveScope(User $user, string $scope): bool
    {
        return \App\Support\RoleScopeMatrix::userIsSuperAdmin($user) || $user->hasRoleForScope($scope);
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
        $rolloutOverrides = $this->rolloutOverridesByScopeRoles($scope, $roleNames);

        foreach ($roleNames as $roleName) {
            $overrides = self::ROLE_MODULE_MODE_OVERRIDES[$roleName] ?? [];

            if (array_key_exists($roleName, $rolloutOverrides)) {
                foreach ($rolloutOverrides[$roleName] as $moduleSlug => $mode) {
                    $overrides[$moduleSlug] = $this->normalizeOverrideModeForResolver($mode);
                }
            }

            $moduleModes = $this->applyRoleModuleModeOverridesMap($overrides, $moduleModes);
        }

        return $moduleModes;
    }

    /**
     * @list<string> $roleNames
     * @return array<string, array<string, string>>
     */
    private function rolloutOverridesByScopeRoles(string $scope, array $roleNames): array
    {
        if ($roleNames === [] || ! $this->isRolloutOverrideEnabled()) {
            return [];
        }

        $overridesByRole = [];

        foreach ($this->overrideManageableModules() as $moduleSlug) {
            $modes = $this->moduleAccessOverrideRepository->listModesForScopeRolesAndModule(
                $scope,
                $roleNames,
                $moduleSlug
            );

            foreach ($modes as $roleName => $mode) {
                $overridesByRole[$roleName][$moduleSlug] = $mode;
            }
        }

        return $overridesByRole;
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
    private function appendRolloutOverrides(array $overrides, string $scope, string $role): array
    {
        if (! $this->isRolloutOverrideEnabled()) {
            return $overrides;
        }

        foreach ($this->overrideManageableModules() as $moduleSlug) {
            $mode = $this->moduleAccessOverrideRepository->findMode($scope, $role, $moduleSlug);
            if (! is_string($mode)) {
                continue;
            }

            $overrides[$moduleSlug] = $this->normalizeOverrideModeForResolver($mode);
        }

        return $overrides;
    }

    private function isRolloutOverrideEnabled(): bool
    {
        return (bool) config('access_control.rollout_override.enabled', config('access_control.pilot_override.enabled', true));
    }
}
