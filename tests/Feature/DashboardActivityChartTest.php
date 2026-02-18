<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardActivityChartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'super-admin']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_desa_user_dashboard_chart_is_scoped_to_own_desa(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'desa', 'area_id' => $desaA->id])->save();
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'A1',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'A2',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'activity_date' => '2026-01-05',
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'B1',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-09',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardStats.total', 2)
                ->where('dashboardStats.this_month', 1)
                ->where('dashboardStats.published', 1)
                ->where('dashboardStats.draft', 1);
        });
    }

    public function test_kecamatan_user_dashboard_chart_includes_own_kecamatan_and_child_desas(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB = Area::create(['name' => 'Kalisalak', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id])->save();
        $user->assignRole('admin-kecamatan');

        Activity::create([
            'title' => 'Kec A',
            'level' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Desa A',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'activity_date' => '2026-01-11',
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kec B',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-12',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Desa B',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-07',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->where('dashboardStats.total', 2)
                ->where('dashboardStats.this_month', 1)
                ->where('dashboardStats.published', 1)
                ->where('dashboardStats.draft', 1)
                ->where('dashboardCharts.level.values', [1, 1]);
        });
    }
}
