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
        Role::create(['name' => 'kecamatan-pokja-ii']);
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
            ->put(route('super-admin.access-control.override.update'), [
                'module' => 'catatan-keluarga',
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
            ->delete(route('super-admin.access-control.override.rollback'), [
                'module' => 'catatan-keluarga',
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
            ->put(route('super-admin.access-control.override.update'), [
                'module' => 'catatan-keluarga',
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertStatus(403);

        $this->assertDatabaseCount('module_access_overrides', 0);
        $this->assertDatabaseCount('module_access_override_audits', 0);
    }

    public function test_override_invalid_ditolak_oleh_validasi_scope_role(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.override.update'), [
                'module' => 'catatan-keluarga',
                'scope' => 'desa',
                'role' => 'kecamatan-pokja-iv',
                'mode' => 'read-only',
            ])
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseCount('module_access_overrides', 0);
        $this->assertDatabaseCount('module_access_override_audits', 0);
    }

    public function test_super_admin_dapat_update_dan_rollback_override_rollout_activities(): void
    {
        $superAdmin = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $superAdmin->assignRole('super-admin');

        $kecamatanPokjaIi = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatan->id,
        ]);
        $kecamatanPokjaIi->assignRole('kecamatan-pokja-ii');

        $this->actingAs($kecamatanPokjaIi)
            ->get('/kecamatan/activities')
            ->assertOk();

        $this->actingAs($superAdmin)
            ->put(route('super-admin.access-control.override.update'), [
                'module' => 'activities',
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-ii',
                'mode' => 'hidden',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'activities',
            'mode' => 'hidden',
            'changed_by' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'activities',
            'before_mode' => 'read-write',
            'after_mode' => 'hidden',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIi)
            ->get('/kecamatan/activities')
            ->assertStatus(403);

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.access-control.override.rollback'), [
                'module' => 'activities',
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-ii',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('module_access_overrides', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'activities',
        ]);

        $this->assertDatabaseHas('module_access_override_audits', [
            'scope' => 'kecamatan',
            'role_name' => 'kecamatan-pokja-ii',
            'module_slug' => 'activities',
            'before_mode' => 'hidden',
            'after_mode' => 'read-write',
            'changed_by' => $superAdmin->id,
        ]);

        $this->actingAs($kecamatanPokjaIi)
            ->get('/kecamatan/activities')
            ->assertOk();
    }
}
