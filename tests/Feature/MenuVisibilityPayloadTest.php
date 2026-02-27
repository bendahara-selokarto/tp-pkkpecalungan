<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuVisibilityPayloadTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatan;

    private Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'desa-sekretaris',
            'desa-pokja-i',
            'kecamatan-sekretaris',
            'kecamatan-pokja-ii',
        ] as $roleName) {
            Role::create(['name' => $roleName]);
        }

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    public function test_payload_sekretaris_berisi_rw_dan_pokja_ro(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-sekretaris');

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.sekretaris-tpk', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i', 'read-only')
                ->where('auth.user.menuGroupModes.pokja-iv', 'read-only')
                ->missing('auth.user.menuGroupModes.referensi')
                ->where('auth.user.moduleModes.buku-keuangan', 'read-write')
                ->where('auth.user.moduleModes.program-prioritas', 'read-write')
                ->where('auth.user.moduleModes.data-warga', 'read-only')
            );
    }

    public function test_payload_pokja_hanya_grup_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $user->assignRole('kecamatan-pokja-ii');

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.pokja-ii', 'read-write')
                ->missing('auth.user.menuGroupModes.referensi')
                ->missing('auth.user.menuGroupModes.sekretaris-tpk')
                ->missing('auth.user.menuGroupModes.monitoring')
                ->where('auth.user.moduleModes.anggota-pokja', 'read-write')
                ->where('auth.user.moduleModes.prestasi-lomba', 'read-write')
                ->missing('auth.user.moduleModes.activities')
                ->missing('auth.user.moduleModes.data-pelatihan-kader')
                ->missing('auth.user.moduleModes.data-warga')
            );
    }

    public function test_payload_multi_role_menggunakan_union_dengan_prioritas_rw(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desa->id,
        ]);
        $user->assignRole('desa-sekretaris');
        $user->assignRole('desa-pokja-i');

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.sekretaris-tpk', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i', 'read-write')
                ->where('auth.user.moduleModes.data-warga', 'read-write')
            );
    }
}
