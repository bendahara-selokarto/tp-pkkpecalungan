<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Services\RoleMenuVisibilityService;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MenuVisibilityPayloadTest extends TestCase
{
    use RefreshDatabase;

    private Area $desaArea;
    private Area $kecamatanArea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kecamatanArea = Area::create([
            'name' => 'Kecamatan Test',
            'level' => 'kecamatan',
        ]);

        $this->desaArea = Area::create([
            'name' => 'Desa Test',
            'level' => 'desa',
            'parent_id' => $this->kecamatanArea->id,
        ]);

        foreach ([
            RoleScopeMatrix::ROLE_SEKRETARIS_DESA,
            RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN,
            RoleScopeMatrix::ROLE_BENDAHARA_DESA,
            RoleScopeMatrix::ROLE_BENDAHARA_KECAMATAN,
            RoleScopeMatrix::ROLE_POKJA_1_DESA,
            RoleScopeMatrix::ROLE_POKJA_2_DESA,
            RoleScopeMatrix::ROLE_POKJA_3_DESA,
            RoleScopeMatrix::ROLE_POKJA_4_DESA,
            RoleScopeMatrix::ROLE_POKJA_1_KECAMATAN,
            RoleScopeMatrix::ROLE_POKJA_2_KECAMATAN,
            RoleScopeMatrix::ROLE_POKJA_3_KECAMATAN,
            RoleScopeMatrix::ROLE_POKJA_4_KECAMATAN,
            RoleScopeMatrix::ROLE_SUPER_ADMIN,
        ] as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }

    public function test_payload_sekretaris_berisi_rw_dan_pokja_ro(): void
    {
        $user = User::factory()->create(['area_id' => $this->desaArea->id]);
        $user->assignRole(RoleScopeMatrix::ROLE_SEKRETARIS_DESA);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.sekretaris-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.penunjang-buku-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.common-pembantu', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i', 'read-only')
                ->where('auth.user.menuGroupModes.pokja-ii', 'read-only')
                ->where('auth.user.menuGroupModes.pokja-iii', 'read-only')
                ->where('auth.user.menuGroupModes.pokja-iv', 'read-only')
                ->where('auth.user.menuGroupModes.bendahara-wajib', 'read-only')
                ->missing('auth.user.menuGroupModes.referensi')
            );
    }

    public function test_payload_pokja_hanya_grup_sendiri(): void
    {
        $user = User::factory()->create(['area_id' => $this->desaArea->id]);
        $user->assignRole(RoleScopeMatrix::ROLE_POKJA_2_DESA);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.pokja-ii-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-ii', 'read-write')
                ->where('auth.user.menuGroupModes.common-pembantu', 'read-write')
                ->missing('auth.user.menuGroupModes.referensi')
                ->missing('auth.user.menuGroupModes.sekretaris-wajib')
                ->missing('auth.user.menuGroupModes.monitoring')
            );
    }

    public function test_payload_bendahara_memiliki_buku_wajib_keuangan(): void
    {
        $user = User::factory()->create(['area_id' => $this->desaArea->id]);
        $user->assignRole(RoleScopeMatrix::ROLE_BENDAHARA_DESA);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.bendahara-wajib', 'read-write')
                ->where('auth.user.moduleModes.buku-keuangan', 'read-write')
                ->where('auth.user.menuGroupModes.common-pembantu', 'read-only')
                ->missing('auth.user.menuGroupModes.sekretaris-wajib')
                ->missing('auth.user.menuGroupModes.pokja-i')
            );
    }

    public function test_payload_desa_pokja_i_memuat_buku_wajib_bantu_dan_bantu_unik_rw(): void
    {
        $user = User::factory()->create(['area_id' => $this->desaArea->id]);
        $user->assignRole(RoleScopeMatrix::ROLE_POKJA_1_DESA);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.pokja-i-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i', 'read-write')
                ->where('auth.user.menuGroupModes.common-pembantu', 'read-write')
                ->where('auth.user.moduleModes.data-kegiatan-pkk-pokja-i', 'read-write')
            );
    }

    public function test_payload_multi_role_menggunakan_union_dengan_prioritas_rw(): void
    {
        $user = User::factory()->create(['area_id' => $this->desaArea->id]);
        $user->assignRole([RoleScopeMatrix::ROLE_SEKRETARIS_DESA, RoleScopeMatrix::ROLE_POKJA_1_DESA]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.sekretaris-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.penunjang-buku-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.pokja-i', 'read-write')
                ->where('auth.user.moduleModes.agenda-surat', 'read-write')
                ->where('auth.user.moduleModes.data-kegiatan-pkk-pokja-i', 'read-write')
            );
    }

    public function test_payload_kecamatan_sekretaris_memiliki_monitoring_desa_read_only(): void
    {
        $user = User::factory()->create(['area_id' => $this->kecamatanArea->id]);
        $user->assignRole(RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('auth.user.menuGroupModes.sekretaris-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.penunjang-buku-wajib', 'read-write')
                ->where('auth.user.menuGroupModes.monitoring', 'read-only')
                ->where('auth.user.moduleModes.activities', 'read-write')
            );
    }
}
