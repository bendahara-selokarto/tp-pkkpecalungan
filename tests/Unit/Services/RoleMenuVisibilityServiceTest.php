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
            'desa-pokja-i',
            'kecamatan-pokja-iii',
            'admin-kecamatan',
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
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_ONLY, $visibility['modules']['program-prioritas'] ?? null);
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

        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['data-keluarga'] ?? null);
        $this->assertSame(RoleMenuVisibilityService::MODE_READ_WRITE, $visibility['modules']['activities'] ?? null);
        $this->assertArrayNotHasKey('data-warga', $visibility['modules']);
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
}
