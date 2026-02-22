<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SuperAdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cache spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Buat permission
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);

        // Buat role super-admin
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());
    }

    #[Test]
    public function super_admin_memiliki_semua_izin()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->assertTrue($user->can('manage users'));
        $this->assertTrue($user->can('manage roles'));
    }

    #[Test]
    public function super_admin_melewati_aturan_gate_before()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user);

        // ability random → tetap true karena Gate::before
        $this->assertTrue(
            $user->can('this permission does not exist')
        );
    }

    #[Test]
    public function super_admin_diarahkan_dari_dashboard_ke_manajemen_pengguna()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('super-admin.users.index', absolute: false));
    }

    #[Test]
    public function peran_super_admin_terlihat_di_samping_profil_pengguna()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get(route('super-admin.users.index'))
            ->assertOk()
            ->assertSee('Super Admin', false);
    }
}


