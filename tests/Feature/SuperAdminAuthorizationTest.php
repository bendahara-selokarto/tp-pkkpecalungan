<?php

namespace Tests\Feature;

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

    /** @test */
    public function super_admin_has_all_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->assertTrue($user->can('manage users'));
        $this->assertTrue($user->can('manage roles'));
    }

    /** @test */
    public function super_admin_bypasses_gate_before_rule()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user);

        // ability random → tetap true karena Gate::before
        $this->assertTrue(
            $user->can('this permission does not exist')
        );
    }

    /** @test */
    public function super_admin_is_redirected_from_dashboard_to_user_management()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('super-admin.users.index', absolute: false));
    }
}
