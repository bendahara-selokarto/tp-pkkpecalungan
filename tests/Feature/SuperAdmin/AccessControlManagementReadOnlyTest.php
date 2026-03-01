<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessControlManagementReadOnlyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin-desa']);
    }

    public function test_super_admin_dapat_melihat_matrix_read_only(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.access-control.index', [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-iv',
                'per_page' => 100,
            ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('filters.scope', 'kecamatan')
                    ->where('filters.role', 'kecamatan-pokja-iv')
                    ->where('filters.page', 1)
                    ->where('filters.per_page', 100)
                    ->where('perPageOptions', [10, 25, 50, 100])
                    ->where('summary.total_rows', static fn (mixed $value): bool => is_int($value) && $value > 0)
                    ->where('pagination.total', static fn (mixed $value): bool => is_int($value) && $value > 0)
                    ->where('pagination.per_page', 100)
                    ->where('pagination.page', 1)
                    ->where('rows', static fn (mixed $rows): bool => collect($rows)->every(
                        static fn (mixed $row): bool => is_array($row) && ($row['role'] ?? null) !== 'super-admin'
                    ))
                    ->where('rows', function (mixed $rows): bool {
                        $target = collect($rows)->first(function (mixed $row): bool {
                            return is_array($row)
                                && ($row['scope'] ?? null) === 'kecamatan'
                                && ($row['role'] ?? null) === 'kecamatan-pokja-iv'
                                && ($row['group'] ?? null) === 'pokja-iv'
                                && ($row['module'] ?? null) === 'catatan-keluarga';
                        });

                        return is_array($target)
                            && ($target['mode'] ?? null) === 'hidden';
                    })
                    ->where('rows', function (mixed $rows): bool {
                        $target = collect($rows)->first(function (mixed $row): bool {
                            return is_array($row)
                                && ($row['scope'] ?? null) === 'kecamatan'
                                && ($row['role'] ?? null) === 'kecamatan-pokja-iv'
                                && ($row['group'] ?? null) === 'pokja-iv'
                                && ($row['module'] ?? null) === 'pilot-project-naskah-pelaporan';
                        });

                        return is_array($target)
                            && ($target['mode'] ?? null) === 'hidden'
                            && ($target['pilot_manageable'] ?? null) === true;
                    })
                    ->where('roleOptions', static fn (mixed $options): bool => collect($options)->every(
                        static fn (mixed $option): bool => is_array($option) && ($option['value'] ?? null) !== 'super-admin'
                    ));
            });
    }

    public function test_filter_scope_dan_mode_berjalan_pada_matrix(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.access-control.index', [
                'scope' => 'desa',
                'mode' => 'read-write',
            ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('filters.scope', 'desa')
                    ->where('filters.mode', 'read-write')
                    ->where('filters.page', 1)
                    ->where('filters.per_page', 25)
                    ->where('pagination.page', 1)
                    ->where('pagination.per_page', 25)
                    ->where('rows', function (mixed $rows): bool {
                        $collection = collect($rows);

                        if ($collection->isEmpty()) {
                            return false;
                        }

                        return $collection->every(static fn (mixed $row): bool => is_array($row)
                            && ($row['scope'] ?? null) === 'desa'
                            && ($row['mode'] ?? null) === 'read-write');
                    });
            });
    }

    public function test_matrix_mendukung_pagination_query_page_dan_per_page(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.access-control.index', [
                'scope' => 'desa',
                'page' => 2,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('filters.scope', 'desa')
                    ->where('filters.page', 2)
                    ->where('filters.per_page', 10)
                    ->where('pagination.page', 2)
                    ->where('pagination.per_page', 10)
                    ->where('pagination.last_page', static fn (mixed $value): bool => is_int($value) && $value >= 2)
                    ->where('pagination.from', static fn (mixed $value): bool => is_int($value) && $value >= 11)
                    ->where('pagination.to', static fn (mixed $value): bool => is_int($value) && $value >= 11)
                    ->where('rows', function (mixed $rows): bool {
                        $collection = collect($rows);

                        return $collection->count() <= 10
                            && $collection->every(static fn (mixed $row): bool => is_array($row)
                                && ($row['scope'] ?? null) === 'desa');
                    });
            });
    }

    public function test_page_melebihi_last_page_di_clamp_ke_halaman_terakhir(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.access-control.index', [
                'scope' => 'desa',
                'page' => 999,
                'per_page' => 10,
            ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('filters.page', static fn (mixed $value): bool => is_int($value) && $value > 1 && $value < 999)
                    ->where('pagination.page', static fn (mixed $value): bool => is_int($value) && $value > 1 && $value < 999)
                    ->where('pagination.last_page', static fn (mixed $value): bool => is_int($value) && $value >= 2)
                    ->where('pagination.from', static fn (mixed $value): bool => is_int($value) && $value >= 1)
                    ->where('pagination.to', static fn (mixed $value): bool => is_int($value) && $value >= 1)
                    ->where('pagination.total', static fn (mixed $value): bool => is_int($value) && $value >= 1);
            });
    }

    public function test_non_super_admin_ditolak_mengakses_matrix_read_only(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $this->actingAs($user)
            ->get(route('super-admin.access-control.index'))
            ->assertStatus(403);
    }
}
