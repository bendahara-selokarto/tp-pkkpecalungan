<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessControlManagementWritePilotTest extends TestCase
{
    use RefreshDatabase;

    private Area $kecamatan;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'kecamatan-pokja-iv']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);
    }

    public function test_super_admin_dapat_update_dan_rollback_override_pilot_catatan_keluarga(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $kecamatanPokjaIv = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $kecamatanPokjaIv->assignRole('kecamatan-pokja-iv');

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/catatan-keluarga')
            ->assertStatus(403);

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.pilot.catatan-keluarga.update'), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'catatan-keluarga',
            'mode' => 'read-only',
            'changed_by' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'catatan-keluarga',
            'before_mode' => 'hidden',
            'after_mode' => 'read-only',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/catatan-keluarga')
            ->assertOk();

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/catatan-keluarga/data-umum-pkk-kecamatan/report/pdf')
            ->assertOk();

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.access-control.pilot.catatan-keluarga.rollback'), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'catatan-keluarga',
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'catatan-keluarga',
            'before_mode' => 'read-only',
            'after_mode' => 'hidden',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/catatan-keluarga')
            ->assertStatus(403);
    }

    public function test_non_super_admin_ditolak_update_override_pilot(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $nonSuperAdmin = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $nonSuperAdmin->assignRole('admin-desa');

        $this->actingAs($nonSuperAdmin)
            ->put(route('super-admin.access-control.pilot.catatan-keluarga.update'), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertStatus(403);

        $this->assertDatabaseCount('module_access_overrides', 0);
        $this->assertDatabaseCount('module_access_override_audits', 0);
    }

    public function test_super_admin_dapat_update_dan_rollback_override_pilot_pilot_project_keluarga_sehat(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $kecamatanPokjaIv = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $kecamatanPokjaIv->assignRole('kecamatan-pokja-iv');

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/pilot-project-keluarga-sehat')
            ->assertStatus(403);

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.pilot.module.update', [
                'moduleSlug' => 'pilot-project-keluarga-sehat',
            ]), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'pilot-project-keluarga-sehat',
            'mode' => 'read-only',
            'changed_by' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'pilot-project-keluarga-sehat',
            'before_mode' => 'hidden',
            'after_mode' => 'read-only',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/pilot-project-keluarga-sehat')
            ->assertOk();

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.access-control.pilot.module.rollback', [
                'moduleSlug' => 'pilot-project-keluarga-sehat',
            ]), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'pilot-project-keluarga-sehat',
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-iv',
            'module_slug' => 'pilot-project-keluarga-sehat',
            'before_mode' => 'read-only',
            'after_mode' => 'hidden',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIv)
            ->get('/kecamatan/pilot-project-keluarga-sehat')
            ->assertStatus(403);
    }

    public function test_override_invalid_ditolak_oleh_validasi_scope_role(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.pilot.catatan-keluarga.update'), [
                'scope' => 'desa',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseCount('module_access_overrides', 0);
        $this->assertDatabaseCount('module_access_override_audits', 0);
    }

    public function test_modul_non_pilot_ditolak_oleh_validasi_endpoint_generic(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.pilot.module.update', [
                'moduleSlug' => 'data-keluarga',
            ]), [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['module_slug']);

        $this->assertDatabaseCount('module_access_overrides', 0);
        $this->assertDatabaseCount('module_access_override_audits', 0);
    }
}
