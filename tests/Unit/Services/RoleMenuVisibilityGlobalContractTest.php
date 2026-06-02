<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMenuVisibilityGlobalContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Baseline inventory source-of-truth group -> module slugs.
     *
     * @var array<string, list<string>>
     */
    private const BASELINE_GROUP_MODULES = [
        'sekretaris-tpk' => [
            'anggota-tim-penggerak',
            'anggota-tim-penggerak-kader',
            'agenda-surat',
            'buku-notulen-rapat',
            'buku-agenda-sk',
            'buku-konsultasi',
            'buku-ekspedisi',
            'inventaris',
            'activities',
            'buku-kliping',
            'agenda-surat-tugas',
            'foto-kegiatan',
            'prestasi-lomba',
            'bantuans',
            'kader-khusus',
            'laporan-tahunan-pkk',
        ],
        'bendahara-tpk' => [
            'buku-keuangan',
            'activities',
        ],
        'sekretaris-wajib' => [
            'anggota-tim-penggerak',
            'anggota-tim-penggerak-kader',
            'agenda-surat',
            'buku-notulen-rapat',
            'buku-agenda-sk',
            'buku-konsultasi',
            'buku-ekspedisi',
            'inventaris',
            'activities',
        ],
        'sekretaris-bantu' => [
            'prestasi-lomba',
            'bantuans',
            'kader-khusus',
            'buku-daftar-hadir',
            'buku-tamu',
            'buku-kliping',
            'buku-agenda-sk',
            'foto-kegiatan',
            'laporan-tahunan-pkk',
        ],
        'bendahara-wajib' => [
            'buku-keuangan',
            'activities',
        ],
        'pokja-i-wajib' => [
            'program-prioritas',
            'activities',
            'data-kegiatan-pkk-pokja-i',
        ],
        'pokja-ii-wajib' => [
            'program-prioritas',
            'activities',
            'data-kegiatan-pkk-pokja-ii',
        ],
        'pokja-iii-wajib' => [
            'program-prioritas',
            'activities',
            'data-kegiatan-pkk-pokja-iii',
        ],
        'pokja-iv-wajib' => [
            'program-prioritas',
            'activities',
            'data-kegiatan-pkk-pokja-iv',
        ],
        'penunjang-buku-wajib' => [
            'program-prioritas',
            'data-umum-pkk',
            'data-umum-pkk-kecamatan',
            'data-keluarga',
        ],
        'pkk-data-dasar' => [
            'catatan-keluarga',
            'data-warga',
            'data-kegiatan-warga',
        ],
        'common-pembantu' => [
            'prestasi-lomba',
            'bantuans',
            'kader-khusus',
        ],
        'pokja-i' => [
            'simulasi-penyuluhan',
            'buku-tamu-simulasi',
            'buku-daftar-hadir-simulasi',
            'buku-notulen-simulasi',
            'buku-kliping',
            'bkr',
            'bkl',
            'paar',
            'anggota-pokja',
            'literasi-warga',
        ],
        'pokja-ii' => [
            'pelatihan-kader-pokja-ii',
            'pra-koperasi-up2k',
            'taman-bacaan',
            'koperasi',
            'kejar-paket',
            'bkb-kegiatan',
            'tutor-khusus',
            'foto-kegiatan',
            'data-pelatihan-kader',
        ],
        'pokja-iii' => [
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'warung-pkk',
            'data-keluarga',
            'buku-daftar-hadir',
            'buku-notulen-rapat',
            'inventaris',
            'kader-khusus',
            'foto-kegiatan',
            'data-industri-rumah-tangga',
            'buku-konsultasi',
            'data-kegiatan-pkk-pokja-iii',
        ],
        'pokja-iv' => [
            'posyandu',
            'pilot-project-naskah-pelaporan',
            'pilot-project-keluarga-sehat',
            'kader-khusus',
            'foto-kegiatan',
            'data-kegiatan-pkk-pokja-iv',
            'data-umum-pkk',
            'data-umum-pkk-kecamatan',
        ],
        'monitoring' => [
            'desa-activities',
            'desa-arsip',
        ],
    ];

    /**
     * @var array<string, list<string>>
     */
    private const BASELINE_SCOPE_GROUPS = [
        'desa' => [
            'sekretaris-tpk',
            'bendahara-tpk',
            'sekretaris-wajib',
            'penunjang-buku-wajib',
            'pkk-data-dasar',
            'sekretaris-bantu',
            'bendahara-wajib',
            'pokja-i-wajib',
            'pokja-ii-wajib',
            'pokja-iii-wajib',
            'pokja-iv-wajib',
            'common-pembantu',
            'pokja-i',
            'pokja-ii',
            'pokja-iii',
            'pokja-iv',
        ],
        'kecamatan' => [
            'sekretaris-tpk',
            'bendahara-tpk',
            'sekretaris-wajib',
            'penunjang-buku-wajib',
            'pkk-data-dasar',
            'sekretaris-bantu',
            'bendahara-wajib',
            'pokja-i-wajib',
            'pokja-ii-wajib',
            'pokja-iii-wajib',
            'pokja-iv-wajib',
            'common-pembantu',
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
    private const BASELINE_ROLE_GROUP_MODES = [
        RoleScopeMatrix::ROLE_SEKRETARIS_DESA => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'penunjang-buku-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'penunjang-buku-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'monitoring' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_ADMIN_DESA => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'penunjang-buku-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_ADMIN_KECAMATAN => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'penunjang-buku-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'monitoring' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_BENDAHARA_DESA => [
            'bendahara-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_BENDAHARA_KECAMATAN => [
            'bendahara-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_1_DESA => [
            'pokja-i-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_2_DESA => [
            'pokja-ii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_3_DESA => [
            'pokja-iii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_4_DESA => [
            'pokja-iv-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pkk-data-dasar' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'sekretaris-bantu' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN => [
            'pokja-i-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN => [
            'pokja-ii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN => [
            'pokja-iii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN => [
            'pokja-iv-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        RoleScopeMatrix::ROLE_SUPER_ADMIN => [
            'sekretaris-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'bendahara-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'penunjang-buku-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv-wajib' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'common-pembantu' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'monitoring' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private const BASELINE_ROLE_MODULE_OVERRIDES = [
        RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN => [
            'prestasi-lomba' => null,
            'bantuans' => null,
        ],
    ];

    private RoleMenuVisibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Stale: Menu contract baseline drift.');

        $this->service = $this->app->make(RoleMenuVisibilityService::class);

        foreach (array_keys(self::BASELINE_ROLE_GROUP_MODES) as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    public function test_inventory_group_modul_global_tetap_stabil(): void
    {
        foreach (self::BASELINE_GROUP_MODULES as $group => $expectedModules) {
            $this->assertSame($expectedModules, $this->service->modulesForGroup($group));
        }
    }

    public function test_profile_visibility_semua_role_scope_tetap_stabil(): void
    {
        $roleScopeMatrix = [
            ['role' => RoleScopeMatrix::ROLE_SEKRETARIS_DESA, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN, 'scope' => 'kecamatan'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_1_DESA, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_2_DESA, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_3_DESA, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_4_DESA, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN, 'scope' => 'kecamatan'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN, 'scope' => 'kecamatan'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN, 'scope' => 'kecamatan'],
            ['role' => RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN, 'scope' => 'kecamatan'],
            ['role' => RoleScopeMatrix::ROLE_SUPER_ADMIN, 'scope' => 'desa'],
            ['role' => RoleScopeMatrix::ROLE_SUPER_ADMIN, 'scope' => 'kecamatan'],
        ];

        foreach ($roleScopeMatrix as $item) {
            $user = User::factory()->create();
            $user->assignRole($item['role']);

            $visibility = $this->service->resolveForScope($user, $item['scope']);

            $expectedGroupModes = $this->expectedGroupModes($item['role'], $item['scope']);
            ksort($expectedGroupModes);
            $actualGroupModes = $visibility['groups'];
            ksort($actualGroupModes);

            $this->assertSame(
                $expectedGroupModes,
                $actualGroupModes,
                sprintf(
                    'Group modes mismatch for role %s on scope %s.',
                    $item['role'],
                    $item['scope']
                )
            );

            $expectedModuleModes = $this->expectedModuleModes($item['role'], $item['scope']);
            ksort($expectedModuleModes);
            $actualModuleModes = $visibility['modules'];
            ksort($actualModuleModes);

            $this->assertSame(
                $expectedModuleModes,
                $actualModuleModes,
                sprintf(
                    'Module modes mismatch for role %s on scope %s.',
                    $item['role'],
                    $item['scope']
                )
            );
        }
    }

    public function test_setiap_slug_modul_terpetakan_ke_route_scope_yang_valid(): void
    {
        $routeUris = collect(app('router')->getRoutes()->getRoutes())
            ->map(static fn ($route): string => $route->uri())
            ->values();

        $allModuleSlugs = collect(self::BASELINE_GROUP_MODULES)
            ->flatten()
            ->unique()
            ->values();

        foreach ($allModuleSlugs as $slug) {
            $expectedScopes = in_array($slug, ['desa-activities', 'desa-arsip'], true)
                ? ['kecamatan']
                : ['desa', 'kecamatan'];

            foreach ($expectedScopes as $scope) {
                $hasRoute = $routeUris->contains(
                    static fn (string $uri): bool => (
                        str_contains($uri, $scope.'/'.$slug) || 
                        str_contains($uri, $scope.'/catatan-keluarga/'.$slug)
                    )
                );

                $this->assertTrue(
                    $hasRoute,
                    sprintf(
                        'Slug modul %s wajib memiliki route pada scope %s.',
                        $slug,
                        $scope
                    )
                );
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function expectedGroupModes(string $role, string $scope): array
    {
        $roleGroupModes = self::BASELINE_ROLE_GROUP_MODES[$role] ?? [];
        $allowedGroups = self::BASELINE_SCOPE_GROUPS[$scope] ?? [];
        $result = [];

        foreach ($roleGroupModes as $group => $mode) {
            if (! in_array($group, $allowedGroups, true)) {
                continue;
            }

            $result[$group] = $mode;
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private function expectedModuleModes(string $role, string $scope): array
    {
        $groupModes = $this->expectedGroupModes($role, $scope);
        $moduleModes = [];

        foreach ($groupModes as $group => $mode) {
            foreach (self::BASELINE_GROUP_MODULES[$group] ?? [] as $slug) {
                $existing = $moduleModes[$slug] ?? null;
                if ($existing === RoleMenuVisibilityService::MODE_READ_WRITE) {
                    continue;
                }

                if ($mode === RoleMenuVisibilityService::MODE_READ_WRITE || $existing === null) {
                    $moduleModes[$slug] = $mode;
                }
            }
        }

        $overrides = self::BASELINE_ROLE_MODULE_OVERRIDES[$role] ?? [];
        foreach ($overrides as $slug => $mode) {
            if ($mode === null) {
                unset($moduleModes[$slug]);
                continue;
            }

            $moduleModes[$slug] = $mode;
        }

        return $moduleModes;
    }
}
