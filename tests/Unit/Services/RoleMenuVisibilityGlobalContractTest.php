<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
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
    private const BASELINE_SCOPE_GROUPS = [
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
    private const BASELINE_ROLE_GROUP_MODES = [
        'desa-sekretaris' => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        'kecamatan-sekretaris' => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_ONLY,
            'monitoring' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
        'desa-pokja-i' => [
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'desa-pokja-ii' => [
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'desa-pokja-iii' => [
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'desa-pokja-iv' => [
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-i' => [
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-ii' => [
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iii' => [
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iv' => [
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'admin-desa' => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'admin-kecamatan' => [
            'sekretaris-tpk' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-i' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-ii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iii' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'pokja-iv' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'monitoring' => RoleMenuVisibilityService::MODE_READ_ONLY,
        ],
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private const BASELINE_ROLE_MODULE_OVERRIDES = [
        'kecamatan-pokja-i' => [
            'data-warga' => null,
            'data-kegiatan-warga' => null,
            'bkl' => null,
            'bkr' => null,
            'paar' => null,
            'anggota-pokja' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'prestasi-lomba' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-ii' => [
            'data-pelatihan-kader' => null,
            'taman-bacaan' => null,
            'koperasi' => null,
            'kejar-paket' => null,
            'anggota-pokja' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'prestasi-lomba' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iii' => [
            'data-keluarga' => null,
            'data-industri-rumah-tangga' => null,
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => null,
            'warung-pkk' => null,
            'anggota-pokja' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'prestasi-lomba' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
        'kecamatan-pokja-iv' => [
            'posyandu' => null,
            'simulasi-penyuluhan' => null,
            'catatan-keluarga' => null,
            'pilot-project-naskah-pelaporan' => null,
            'pilot-project-keluarga-sehat' => null,
            'anggota-pokja' => RoleMenuVisibilityService::MODE_READ_WRITE,
            'prestasi-lomba' => RoleMenuVisibilityService::MODE_READ_WRITE,
        ],
    ];

    private RoleMenuVisibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(RoleMenuVisibilityService::class);

        foreach (array_keys(self::BASELINE_ROLE_GROUP_MODES) as $roleName) {
            Role::create(['name' => $roleName]);
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
            ['role' => 'desa-sekretaris', 'scope' => 'desa'],
            ['role' => 'kecamatan-sekretaris', 'scope' => 'kecamatan'],
            ['role' => 'desa-pokja-i', 'scope' => 'desa'],
            ['role' => 'desa-pokja-ii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iv', 'scope' => 'desa'],
            ['role' => 'kecamatan-pokja-i', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-ii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iv', 'scope' => 'kecamatan'],
            ['role' => 'admin-desa', 'scope' => 'desa'],
            ['role' => 'admin-kecamatan', 'scope' => 'kecamatan'],
        ];

        foreach ($roleScopeMatrix as $item) {
            $user = User::factory()->create();
            $user->assignRole($item['role']);

            $visibility = $this->service->resolveForScope($user, $item['scope']);

            $expectedGroupModes = $this->expectedGroupModes($item['role'], $item['scope']);
            $this->assertSame(
                $expectedGroupModes,
                $visibility['groups'],
                sprintf(
                    'Group modes mismatch untuk role %s pada scope %s.',
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
                    'Module modes mismatch untuk role %s pada scope %s.',
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
                    static fn (string $uri): bool => str_starts_with($uri, $scope.'/'.$slug)
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
