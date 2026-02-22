<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserScopePresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);
    }

    public function test_index_menggunakan_scope_canonical_dari_level_area(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        $managedUser = User::factory()->create([
            'scope' => ScopeLevel::DESA->value,
            'area_id' => $kecamatan->id,
        ]);
        $managedUser->assignRole('admin-kecamatan');

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($managedUser) {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('users.data', function (mixed $users) use ($managedUser): bool {
                    $target = collect($users)->firstWhere('id', $managedUser->id);

                    return is_array($target)
                        && ($target['scope'] ?? null) === ScopeLevel::KECAMATAN->value;
                });
        });
    }

    public function test_edit_menggunakan_scope_canonical_dari_level_area(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => ScopeLevel::DESA->value,
            'parent_id' => $kecamatan->id,
        ]);

        $managedUser = User::factory()->create([
            'scope' => ScopeLevel::KECAMATAN->value,
            'area_id' => $desa->id,
        ]);
        $managedUser->assignRole('admin-desa');

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.edit', $managedUser));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Edit')
                ->where('user.scope', ScopeLevel::DESA->value);
        });
    }

    public function test_create_tidak_mengekspos_role_super_admin_pada_opsi_assignable(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.create'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Create')
                ->where('roleOptionsByScope.kecamatan', ['kecamatan-sekretaris']);
        });
    }

    public function test_edit_tidak_mengekspos_role_super_admin_pada_opsi_assignable(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);
        $managedUser = User::factory()->create([
            'scope' => ScopeLevel::KECAMATAN->value,
            'area_id' => $kecamatan->id,
        ]);
        $managedUser->assignRole('admin-kecamatan');

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.edit', $managedUser));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Edit')
                ->where('roleOptionsByScope.kecamatan', ['kecamatan-sekretaris']);
        });
    }
}
