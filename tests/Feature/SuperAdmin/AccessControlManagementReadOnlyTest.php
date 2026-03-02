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
            ->get(route('super-admin.access-control.index'))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('summary.total_rows', static fn (mixed $value): bool => is_int($value) && $value > 0)
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
                    });
            });
    }

    public function test_matrix_menandai_modul_rollout_activities_sebagai_override_manageable(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.access-control.index', [
                'scope' => 'kecamatan',
                'role' => 'kecamatan-pokja-ii',
            ]))
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('SuperAdmin/AccessControl/Index')
                    ->where('rows', function (mixed $rows): bool {
                        $target = collect($rows)->first(function (mixed $row): bool {
                            return is_array($row)
                                && ($row['scope'] ?? null) === 'kecamatan'
                                && ($row['role'] ?? null) === 'kecamatan-pokja-ii'
                                && ($row['module'] ?? null) === 'activities';
                        });

                        return is_array($target)
                            && ($target['override_manageable'] ?? null) === true;
                    });
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

    public function test_non_super_admin_ditolak_mengakses_matrix_read_only(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin-desa');

        $this->actingAs($user)
            ->get(route('super-admin.access-control.index'))
            ->assertStatus(403);
    }
}
