<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleMenuVisibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoleMenuVisibilityService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(RoleMenuVisibilityService::class);

        foreach ([
            'desa-sekretaris',
            'kecamatan-sekretaris',
            'desa-pokja-ii',
            'desa-pokja-iii',
            'desa-pokja-iv',
            'kecamatan-pokja-i',
            'kecamatan-pokja-ii',
            'desa-pokja-i',
            'kecamatan-pokja-iii',
            'kecamatan-pokja-iv',
            'admin-desa',
            'admin-kecamatan',
            'super-admin',
        ] as $roleName) {
            Role::create(['name' => $roleName]);
        }
    }

    public function test_desa_sekretaris_memiliki_sekretaris_rw_dan_pokja_ro(): void
    {
        $user = User::factory()->create();
        $user->assignRole('desa-sekretaris');

        $visibility = $this->service->resolveForScope($user, 'desa');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['sekretaris-tpk'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['pokja-i'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['pokja-iv'] ?? null);
        $this->assertArrayNotHasKey('monitoring', $visibility['groups']);
        $this->assertArrayNotHasKey('referensi', $visibility['groups']);

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['buku-keuangan'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['activities'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['anggota-tim-penggerak-kader'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['laporan-tahunan-pkk'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['data-warga'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['program-prioritas'] ?? null);
    }

    public function test_pokja_hanya_memiliki_grup_sendiri(): void
    {
        $user = User::factory()->create();
        $user->assignRole('kecamatan-pokja-iii');

        $visibility = $this->service->resolveForScope($user, 'kecamatan');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['pokja-iii'] ?? null);
        $this->assertArrayNotHasKey('referensi', $visibility['groups']);
        $this->assertArrayNotHasKey('pokja-i', $visibility['groups']);
        $this->assertArrayNotHasKey('sekretaris-tpk', $visibility['groups']);
        $this->assertArrayNotHasKey('monitoring', $visibility['groups']);

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['anggota-pokja'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['prestasi-lomba'] ?? null);
        $this->assertArrayNotHasKey('data-keluarga', $visibility['modules']);
        $this->assertArrayNotHasKey('activities', $visibility['modules']);
        $this->assertCount(2, $visibility['modules']);
    }

    public function test_kecamatan_pokja_ii_hanya_memiliki_dua_modul_rw(): void
    {
        $user = User::factory()->create();
        $user->assignRole('kecamatan-pokja-ii');

        $visibility = $this->service->resolveForScope($user, 'kecamatan');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['anggota-pokja'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['prestasi-lomba'] ?? null);
        $this->assertArrayNotHasKey('activities', $visibility['modules']);
        $this->assertArrayNotHasKey('data-pelatihan-kader', $visibility['modules']);
        $this->assertArrayNotHasKey('taman-bacaan', $visibility['modules']);
        $this->assertArrayNotHasKey('koperasi', $visibility['modules']);
        $this->assertArrayNotHasKey('kejar-paket', $visibility['modules']);
        $this->assertCount(2, $visibility['modules']);
    }

    public function test_admin_kecamatan_kompatibel_rw_dengan_monitoring_ro(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin-kecamatan');

        $visibility = $this->service->resolveForScope($user, 'kecamatan');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['sekretaris-tpk'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['pokja-ii'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['monitoring'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['desa-activities'] ?? null);
    }

    public function test_semua_role_operasional_memiliki_menu_buku_kegiatan(): void
    {
        $roleScopeMatrix = [
            ['role' => 'desa-sekretaris', 'scope' => 'desa'],
            ['role' => 'kecamatan-sekretaris', 'scope' => 'kecamatan'],
            ['role' => 'desa-pokja-i', 'scope' => 'desa'],
            ['role' => 'desa-pokja-ii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iv', 'scope' => 'desa'],
            ['role' => 'admin-desa', 'scope' => 'desa'],
            ['role' => 'admin-kecamatan', 'scope' => 'kecamatan'],
            ['role' => 'super-admin', 'scope' => 'desa'],
            ['role' => 'super-admin', 'scope' => 'kecamatan'],
        ];

        foreach ($roleScopeMatrix as $item) {
            $user = User::factory()->create();
            $user->assignRole($item['role']);

            $visibility = $this->service->resolveForScope($user, $item['scope']);

            $this->assertSame(
                RoleMenuVisibilityService::MODE_READ_WRITE,
                $visibility['modules']['activities'] ?? null,
                sprintf(
                    'Role %s pada scope %s harus memiliki modul activities (Buku Kegiatan).',
                    $item['role'],
                    $item['scope']
                )
            );
        }
    }

    public function test_semua_role_pokja_memiliki_anggota_pokja_dan_prestasi_lomba_rw(): void
    {
        $roleScopeMatrix = [
            ['role' => 'desa-pokja-i', 'scope' => 'desa'],
            ['role' => 'desa-pokja-ii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iv', 'scope' => 'desa'],
            ['role' => 'kecamatan-pokja-i', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-ii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iv', 'scope' => 'kecamatan'],
        ];

        foreach ($roleScopeMatrix as $item) {
            $user = User::factory()->create();
            $user->assignRole($item['role']);

            $visibility = $this->service->resolveForScope($user, $item['scope']);

            $this->assertSame(
                RoleMenuVisibilityService::MODE_READ_WRITE,
                $visibility['modules']['anggota-pokja'] ?? null,
                sprintf(
                    'Role %s pada scope %s harus RW pada modul anggota-pokja.',
                    $item['role'],
                    $item['scope']
                )
            );

            $this->assertSame(
                RoleMenuVisibilityService::MODE_READ_WRITE,
                $visibility['modules']['prestasi-lomba'] ?? null,
                sprintf(
                    'Role %s pada scope %s harus RW pada modul prestasi-lomba.',
                    $item['role'],
                    $item['scope']
                )
            );
        }
    }

    public function test_semua_pokja_kecamatan_hanya_memiliki_dua_menu(): void
    {
        $kecamatanPokjaRoles = [
            'kecamatan-pokja-i',
            'kecamatan-pokja-ii',
            'kecamatan-pokja-iii',
            'kecamatan-pokja-iv',
        ];

        foreach ($kecamatanPokjaRoles as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $visibility = $this->service->resolveForScope($user, 'kecamatan');

            $this->assertSame(
                ['anggota-pokja', 'prestasi-lomba'],
                array_keys($visibility['modules']),
                sprintf('Role %s harus hanya memiliki 2 menu modul.', $role)
            );
        }
    }

    public function test_modul_buku_sekretaris_hanya_terpetakan_pada_group_sekretaris(): void
    {
        $bukuSekretarisModules = [
            'buku-notulen-rapat',
            'buku-daftar-hadir',
            'buku-tamu',
            'program-prioritas',
        ];

        $sekretarisModules = $this->service->modulesForGroup('sekretaris-tpk');
        foreach ($bukuSekretarisModules as $moduleSlug) {
            $this->assertContains($moduleSlug, $sekretarisModules);
        }

        foreach (['pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv', 'monitoring'] as $group) {
            $groupModules = $this->service->modulesForGroup($group);

            foreach ($bukuSekretarisModules as $moduleSlug) {
                $this->assertNotContains(
                    $moduleSlug,
                    $groupModules,
                    sprintf('Module %s tidak boleh dipetakan ke group %s.', $moduleSlug, $group)
                );
            }
        }
    }

    public function test_role_pokja_tidak_mendapat_modul_buku_sekretaris(): void
    {
        $bukuSekretarisModules = [
            'buku-notulen-rapat',
            'buku-daftar-hadir',
            'buku-tamu',
            'program-prioritas',
        ];

        $roleScopeMatrix = [
            ['role' => 'desa-pokja-i', 'scope' => 'desa'],
            ['role' => 'desa-pokja-ii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iii', 'scope' => 'desa'],
            ['role' => 'desa-pokja-iv', 'scope' => 'desa'],
            ['role' => 'kecamatan-pokja-i', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-ii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iii', 'scope' => 'kecamatan'],
            ['role' => 'kecamatan-pokja-iv', 'scope' => 'kecamatan'],
        ];

        foreach ($roleScopeMatrix as $item) {
            $user = User::factory()->create();
            $user->assignRole($item['role']);

            $visibility = $this->service->resolveForScope($user, $item['scope']);

            foreach ($bukuSekretarisModules as $moduleSlug) {
                $this->assertArrayNotHasKey(
                    $moduleSlug,
                    $visibility['modules'],
                    sprintf(
                        'Role %s pada scope %s tidak boleh memiliki modul %s.',
                        $item['role'],
                        $item['scope'],
                        $moduleSlug
                    )
                );
            }
        }
    }
}
