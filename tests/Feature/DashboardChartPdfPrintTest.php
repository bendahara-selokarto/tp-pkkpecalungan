<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardChartPdfPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'super-admin']);
    }

    public function test_pengguna_desa_bisa_mencetak_chart_dashboard_ke_pdf(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Rapat PKK',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.charts.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_dialihkan_dari_cetak_chart_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get(route('dashboard.charts.report'));

        $response->assertRedirect(route('super-admin.users.index'));
    }
}
