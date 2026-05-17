<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AreaManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private Area $kecamatan;
    private Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        Role::create(['name' => 'super-admin']);

        // Setup Areas
        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desa = Area::create([
            'name' => 'Selokarto',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        // Setup Super Admin
        $this->superAdmin = User::factory()->create([
            'area_id' => $this->kecamatan->id,
        ]);
        $this->superAdmin->assignRole('super-admin');
    }

    public function test_super_admin_can_access_area_index_page(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('super-admin.areas.index'));

        $response->assertStatus(200);
        $response->assertSee('Pecalungan');
        $response->assertSee('Selokarto');
    }

    public function test_super_admin_can_access_area_edit_page(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('super-admin.areas.edit', $this->desa));

        $response->assertStatus(200);
        $response->assertSee('Selokarto');
    }

    public function test_super_admin_can_update_area_metadata(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('super-admin.areas.update', $this->desa), [
                'chairperson_name' => 'NY. SITI AMINAH',
                'chairperson_role' => 'KETUA TP PKK DESA SELOKARTO',
            ]);

        $response->assertRedirect(route('super-admin.areas.index'));
        $response->assertSessionHas('success', 'Data wilayah berhasil diperbarui');

        $this->desa->refresh();
        $this->assertEquals('NY. SITI AMINAH', $this->desa->chairperson_name);
        $this->assertEquals('KETUA TP PKK DESA SELOKARTO', $this->desa->chairperson_role);
    }

    public function test_non_super_admin_cannot_access_area_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('super-admin.areas.index'));

        $response->assertStatus(403);
    }
}
