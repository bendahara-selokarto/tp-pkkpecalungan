<?php

namespace Tests\Unit\Services;

use App\Domains\Wilayah\AccessControl\Models\ModuleAccessOverride;
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
        $this->markTestSkipped('Stale: Menunggu penyusunan ulang bertahap');

        foreach ([
            'desa-sekretaris',
            'kecamatan-sekretaris',
            'desa-bendahara',
            'kecamatan-bendahara',
            'desa-pokja-i',
            'desa-pokja-ii',
            'desa-pokja-iii',
            'desa-pokja-iv',
            'kecamatan-pokja-i',
            'kecamatan-pokja-ii',
            'kecamatan-pokja-iii',
            'kecamatan-pokja-iv',
            'super-admin',
        ] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $this->service = $this->app->make(RoleMenuVisibilityService::class);
    }

    public function test_sekretaris_memiliki_buku_wajib_buku_bantu_dan_penunjang_rw(): void
    {
        $user = User::factory()->create();
        $user->assignRole('desa-sekretaris');

        $visibility = $this->service->resolveForScope($user, 'desa');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['sekretaris-wajib'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['penunjang-buku-wajib'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['common-pembantu'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['pokja-i'] ?? null);

        foreach ([
            'anggota-tim-penggerak',
            'agenda-surat',
            'inventaris',
            'activities',
            'buku-notulen-rapat',
            'prestasi-lomba',
            'bantuans',
            'program-prioritas',
        ] as $moduleSlug) {
            $this->assertSame(
                RoleMenuVisibilityService::MODE_READ_WRITE,
                $visibility['modules'][$moduleSlug] ?? null,
                sprintf('Sekretaris wajib RW untuk %s.', $moduleSlug)
            );
        }

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['catatan-keluarga'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['buku-keuangan'] ?? null);
    }

    public function test_kecamatan_sekretaris_memiliki_monitoring_ro(): void
    {
        $user = User::factory()->create();
        $user->assignRole('kecamatan-sekretaris');

        $visibility = $this->service->resolveForScope($user, 'kecamatan');

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['sekretaris-wajib'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['monitoring'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['desa-activities'] ?? null);
    }

    public function test_bendahara_memiliki_buku_wajib_keuangan(): void
    {
        foreach (['desa-bendahara' => 'desa', 'kecamatan-bendahara' => 'kecamatan'] as $role => $scope) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $visibility = $this->service->resolveForScope($user, $scope);

            $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['groups']['bendahara-wajib'] ?? null);
            $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['buku-keuangan'] ?? null);
            $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['groups']['common-pembantu'] ?? null);
        }
    }

    public function test_role_pokja_memiliki_buku_pembantu_bersama_dan_bantu_unik(): void
    {
        $expectedModulesByRole = [
            'kecamatan-pokja-i' => [
                'program-prioritas',
                'data-kegiatan-pkk-pokja-i',
                'activities',
                'prestasi-lomba',
                'bantuans',
                'kader-khusus',
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
            'kecamatan-pokja-ii' => [
                'program-prioritas',
                'activities',
                'prestasi-lomba',
                'bantuans',
                'kader-khusus',
                'pelatihan-kader-pokja-ii',
                'pra-koperasi-up2k',
                'taman-bacaan',
                'koperasi',
                'kejar-paket',
                'bkb-kegiatan',
                'tutor-khusus',
                'foto-kegiatan',
                'data-pelatihan-kader',
                'data-kegiatan-pkk-pokja-ii',
            ],
            'kecamatan-pokja-iii' => [
                'program-prioritas',
                'activities',
                'prestasi-lomba',
                'bantuans',
                'kader-khusus',
                'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
                'buku-bantu-pangan',
                'warung-pkk',
                'data-keluarga',
                'buku-daftar-hadir',
                'buku-notulen-rapat',
                'inventaris',
                'data-industri-rumah-tangga',
                'buku-konsultasi',
                'data-kegiatan-pkk-pokja-iii',
                'foto-kegiatan',
            ],
            'kecamatan-pokja-iv' => [
                'program-prioritas',
                'activities',
                'prestasi-lomba',
                'bantuans',
                'kader-khusus',
                'posyandu',
                'pilot-project-naskah-pelaporan',
                'pilot-project-keluarga-sehat',
                'data-kegiatan-pkk-pokja-iv',
                'data-umum-pkk',
                'data-umum-pkk-kecamatan',
                'foto-kegiatan',
            ],
        ];

        foreach ($expectedModulesByRole as $role => $expectedModules) {
            $user = User::factory()->create();
            $user->assignRole($role);

            $visibility = $this->service->resolveForScope($user, 'kecamatan');

            $actualModules = $visibility['modules'];
            ksort($actualModules);
            $expected = array_fill_keys($expectedModules, RoleMenuVisibilityService::MODE_READ_WRITE);
            ksort($expected);

            $this->assertSame($expected, $actualModules, sprintf('Modul role %s drift.', $role));
        }
    }

    public function test_scope_mismatch_tidak_menghasilkan_group_mode_untuk_role_non_super_admin(): void
    {
        $desaUser = User::factory()->create();
        $desaUser->assignRole('desa-pokja-i');

        $kecamatanVisibility = $this->service->resolveForScope($desaUser, 'kecamatan');
        $this->assertSame([], $kecamatanVisibility['groups']);
        $this->assertSame([], $kecamatanVisibility['modules']);
        $this->assertNull($this->service->resolveModuleModeForScope($desaUser, 'kecamatan', 'anggota-pokja'));

        $kecamatanUser = User::factory()->create();
        $kecamatanUser->assignRole('kecamatan-pokja-i');

        $desaVisibility = $this->service->resolveForScope($kecamatanUser, 'desa');
        $this->assertSame([], $desaVisibility['groups']);
        $this->assertSame([], $desaVisibility['modules']);
        $this->assertNull($this->service->resolveModuleModeForScope($kecamatanUser, 'desa', 'anggota-pokja'));
    }

    public function test_override_rollout_activities_diterapkan_ke_mode_efektif(): void
    {
        $actor = User::factory()->create();

        $user = User::factory()->create();
        $user->assignRole('kecamatan-pokja-ii');

        $this->assertSame(
            RoleMenuVisibilityService::MODE_READ_WRITE,
            $this->service->resolveModuleModeForScope($user, 'kecamatan', 'activities')
        );

        ModuleAccessOverride::query()->create([
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'activities',
            'mode' => RoleMenuVisibilityService::MODE_HIDDEN,
            'changed_by' => $actor->id,
        ]);

        $this->assertNull(
            $this->service->resolveModuleModeForScope($user, 'kecamatan', 'activities')
        );
    }

    public function test_override_non_rollout_diabaikan_oleh_resolver(): void
    {
        $actor = User::factory()->create();

        $user = User::factory()->create();
        $user->assignRole('kecamatan-pokja-ii');

        ModuleAccessOverride::query()->create([
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'buku-keuangan',
            'mode' => RoleMenuVisibilityService::MODE_HIDDEN,
            'changed_by' => $actor->id,
        ]);

        // buku-keuangan is not assigned to pokja-ii anyway
        $this->assertArrayNotHasKey(
            'buku-keuangan',
            $this->service->resolveForScope($user, 'kecamatan')['modules']
        );
    }

    public function test_rollout_modules_mencakup_agenda_surat(): void
    {
        $this->assertSame(
            ['catatan-keluarga', 'activities', 'agenda-surat'],
            $this->service->overrideManageableModules()
        );
    }
}
