<?php

namespace Tests\Feature\SuperAdmin;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementIndexPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
    }

    public function test_super_admin_melihat_index_user_management_dengan_pagination_default_10(): void
    {
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
        ]);
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        for ($i = 1; $i <= 12; $i++) {
            $user = User::factory()->create([
                'name' => sprintf('Managed User %02d', $i),
                'scope' => ScopeLevel::KECAMATAN->value,
                'area_id' => $kecamatan->id,
            ]);
            $user->assignRole('admin-kecamatan');
        }

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('users.per_page', 10)
                ->where('users.total', 13)
                ->has('users.data', 10);
        });
    }

    public function test_super_admin_dapat_mengubah_per_page_user_management_ke_nilai_valid(): void
    {
        $superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        for ($i = 1; $i <= 30; $i++) {
            $user = User::factory()->create([
                'name' => sprintf('Managed User %02d', $i),
                'scope' => ScopeLevel::KECAMATAN->value,
                'area_id' => $kecamatan->id,
            ]);
            $user->assignRole('admin-kecamatan');
        }

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index', ['per_page' => 25]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('filters.per_page', 25)
                ->where('users.per_page', 25)
                ->has('users.data', 25);
        });
    }

    public function test_super_admin_per_page_invalid_user_management_fallback_ke_default(): void
    {
        $superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        for ($i = 1; $i <= 15; $i++) {
            $user = User::factory()->create([
                'name' => sprintf('Managed User %02d', $i),
                'scope' => ScopeLevel::KECAMATAN->value,
                'area_id' => $kecamatan->id,
            ]);
            $user->assignRole('admin-kecamatan');
        }

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index', ['per_page' => 999]));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('filters.per_page', 10)
                ->where('users.per_page', 10)
                ->has('users.data', 10);
        });
    }

    public function test_link_pagination_user_management_mempertahankan_query_per_page_saat_navigasi_halaman(): void
    {
        $superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $superAdmin->assignRole('super-admin');

        $kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => ScopeLevel::KECAMATAN->value,
        ]);

        for ($i = 1; $i <= 35; $i++) {
            $user = User::factory()->create([
                'name' => sprintf('Managed User %02d', $i),
                'scope' => ScopeLevel::KECAMATAN->value,
                'area_id' => $kecamatan->id,
            ]);
            $user->assignRole('admin-kecamatan');
        }

        $pageOneResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index', ['per_page' => 25]));

        $pageOneResponse->assertOk();
        $pageOneResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('filters.per_page', 25)
                ->where('users.current_page', 1)
                ->where('users.next_page_url', static function (mixed $url): bool {
                    if (! is_string($url)) {
                        return false;
                    }

                    return str_contains($url, 'page=2')
                        && str_contains($url, 'per_page=25');
                });
        });

        $pageTwoResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index', ['page' => 2, 'per_page' => 25]));

        $pageTwoResponse->assertOk();
        $pageTwoResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('filters.per_page', 25)
                ->where('users.current_page', 2)
                ->has('users.data', 11);
        });

        $backToPageOneResponse = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index', ['page' => 1, 'per_page' => 25]));

        $backToPageOneResponse->assertOk();
        $backToPageOneResponse->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('filters.per_page', 25)
                ->where('users.current_page', 1);
        });
    }

    public function test_index_memetakan_scope_dan_area_null_untuk_user_tanpa_area_id(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $targetUser = User::factory()->create([
            'name' => 'User Tanpa Area',
            'scope' => ScopeLevel::DESA->value,
            'area_id' => null,
        ]);
        $targetUser->assignRole('admin-desa');

        $response = $this->actingAs($superAdmin)
            ->get(route('super-admin.users.index'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($targetUser): void {
            $page
                ->component('SuperAdmin/Users/Index')
                ->where('users.data', function (mixed $users) use ($targetUser): bool {
                    $target = collect($users)->firstWhere('id', $targetUser->id);

                    return is_array($target)
                        && ($target['scope'] ?? null) === null
                        && ($target['area'] ?? null) === null;
                });
        });
    }

    public function test_non_super_admin_ditolak_mengakses_index_user_management(): void
    {
        $nonSuperAdmin = User::factory()->create();
        $nonSuperAdmin->assignRole('admin-desa');

        $response = $this->actingAs($nonSuperAdmin)
            ->get(route('super-admin.users.index'));

        $response->assertStatus(403);
    }
}
