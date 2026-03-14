<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'super-admin']);
        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
    }

    #[Test]
    public function super_admin_tidak_dapat_menghapus_super_admin_lain()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $target = User::factory()->create();
        $target->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)
            ->delete(route('super-admin.users.destroy', $target));

        $response
            ->assertRedirect(route('super-admin.users.index'))
            ->assertSessionHas('error', 'Super Admin tidak boleh dihapus');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
        ]);
    }

    #[Test]
    public function super_admin_tidak_dapat_memperbarui_super_admin_lain()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $target = User::factory()->create([
            'name' => 'Super Admin Target',
            'email' => 'super-admin-target@example.com',
        ]);
        $target->assignRole('super-admin');

        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
        ]);

        $response = $this->actingAs($superAdmin)
            ->put(route('super-admin.users.update', $target), [
                'name' => 'Nama Baru',
                'email' => 'baru@example.com',
                'scope' => 'desa',
                'area_id' => $desa->id,
                'role' => 'desa-sekretaris',
            ]);

        $response
            ->assertRedirect(route('super-admin.users.index'))
            ->assertSessionHas('error', 'Super Admin tidak boleh diubah');

        $target->refresh();

        $this->assertSame('Super Admin Target', $target->name);
        $this->assertSame('super-admin-target@example.com', $target->email);
        $this->assertTrue($target->hasRole('super-admin'));
    }

    #[Test]
    public function super_admin_tidak_dapat_membuat_user_dengan_role_super_admin_dari_form_manajemen()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $response = $this->actingAs($superAdmin)
            ->post(route('super-admin.users.store'), [
                'name' => 'Tidak Boleh Assign Super Admin',
                'email' => 'blocked-create-super-admin@example.com',
                'password' => 'password123',
                'scope' => 'kecamatan',
                'area_id' => $kecamatan->id,
                'role' => 'super-admin',
            ]);

        $response
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'blocked-create-super-admin@example.com',
        ]);
    }

    #[Test]
    public function super_admin_tidak_dapat_mengubah_role_user_managed_menjadi_super_admin()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $target = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatan->id,
        ]);
        $target->assignRole('kecamatan-sekretaris');

        $response = $this->actingAs($superAdmin)
            ->put(route('super-admin.users.update', $target), [
                'name' => 'Target User Updated',
                'email' => 'target-user-updated@example.com',
                'scope' => 'kecamatan',
                'area_id' => $kecamatan->id,
                'role' => 'super-admin',
            ]);

        $response
            ->assertSessionHasErrors(['role']);

        $target->refresh();
        $this->assertFalse($target->hasRole('super-admin'));
        $this->assertTrue($target->hasRole('kecamatan-sekretaris'));
    }
}
