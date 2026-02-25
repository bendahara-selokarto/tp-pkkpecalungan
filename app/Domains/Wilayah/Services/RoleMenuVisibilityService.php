<?php

namespace App\Domains\Wilayah\Services;

use App\Models\User;

class RoleMenuVisibilityService
{
    public const MODE_READ_ONLY = 'read-only';

    public const MODE_READ_WRITE = 'read-write';

    /**
     * @var array<string, list<string>>
     */
    private const GROUP_MODULES = [
        'sekretaris-tpk' => [
            'anggota-tim-penggerak',
            'anggota-tim-penggerak-kader',
            'kader-khusus',
            'agenda-surat',
            'buku-keuangan',
            'bantuans',
            'inventaris',
            'activities',
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
            'program-prioritas',
            'pilot-project-naskah-pelaporan',
            'pilot-project-keluarga-sehat',
        ],
        'monitoring' => [
            'desa-activities',
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
        'super-admin' => [
            'sekretaris-tpk' => self::MODE_READ_WRITE,
            'pokja-i' => self::MODE_READ_WRITE,
            'pokja-ii' => self::MODE_READ_WRITE,
            'pokja-iii' => self::MODE_READ_WRITE,
            'pokja-iv' => self::MODE_READ_WRITE,
            'monitoring' => self::MODE_READ_WRITE,
        ],
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private const ROLE_MODULE_MODE_OVERRIDES = [
        // Modul pokja tertentu diturunkan menjadi read-only untuk role kecamatan pokja.
        'kecamatan-pokja-i' => [
            'activities' => null,
            'data-warga' => null,
            'data-kegiatan-warga' => null,
            'bkl' => null,
            'bkr' => null,
            'paar' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-ii' => [
            'activities' => null,
            'data-pelatihan-kader' => null,
            'taman-bacaan' => null,
            'koperasi' => null,
            'kejar-paket' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iii' => [
            'activities' => null,
            'data-keluarga' => null,
            'data-industri-rumah-tangga' => null,
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => null,
            'warung-pkk' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iv' => [
            'activities' => null,
            'posyandu' => null,
            'simulasi-penyuluhan' => null,
            'catatan-keluarga' => null,
            'program-prioritas' => null,
            'pilot-project-naskah-pelaporan' => null,
            'pilot-project-keluarga-sehat' => null,
            'anggota-pokja' => self::MODE_READ_WRITE,
            'prestasi-lomba' => self::MODE_READ_WRITE,
        ],
    ];

    /**
     * @return array{groups: array<string, string>, modules: array<string, string>}
     */
    public function resolveForScope(User $user, string $scope): array
    {
        $groupModes = $this->resolveGroupModesForScope($user, $scope);
        $moduleModes = $this->resolveModuleModes($groupModes);
        $moduleModes = $this->applyRoleModuleModeOverrides($user, $moduleModes);

        return [
            'groups' => $groupModes,
            'modules' => $moduleModes,
        ];
    }

    public function resolveModuleModeForScope(User $user, string $scope, string $moduleSlug): ?string
    {
        $visibility = $this->resolveForScope($user, $scope);

        return $visibility['modules'][$moduleSlug] ?? null;
    }

    /**
     * @return list<string>
     */
    public function modulesForGroup(string $group): array
    {
        return self::GROUP_MODULES[$group] ?? [];
    }

    /**
     * @return array<string, string>
     */
    private function resolveGroupModesForScope(User $user, string $scope): array
    {
        $allowedGroups = self::GROUPS_BY_SCOPE[$scope] ?? [];
        if ($allowedGroups === []) {
            return [];
        }

        $groupModes = [];
        foreach ($user->getRoleNames() as $roleName) {
            $roleModes = self::ROLE_GROUP_MODES[(string) $roleName] ?? [];
            foreach ($roleModes as $group => $mode) {
                if (! in_array($group, $allowedGroups, true)) {
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
    private function applyRoleModuleModeOverrides(User $user, array $moduleModes): array
    {
        foreach ($user->getRoleNames() as $roleName) {
            $overrides = self::ROLE_MODULE_MODE_OVERRIDES[(string) $roleName] ?? [];
            foreach ($overrides as $moduleSlug => $mode) {
                if ($mode === null) {
                    unset($moduleModes[$moduleSlug]);
                    continue;
                }

                $moduleModes[$moduleSlug] = $mode;
            }
        }

        return $moduleModes;
    }
}
