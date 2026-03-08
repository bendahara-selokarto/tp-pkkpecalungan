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

    private const ACTIVE_BUDGET_YEAR = 2026;

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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Rapat PKK',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-01-15',
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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

    public function test_template_pdf_dashboard_menampilkan_tahun_anggaran_aktif(): void
    {
        $user = User::factory()->make([
            'name' => 'Admin Desa',
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->setRelation('area', Area::make(['name' => 'Gombong', 'level' => 'desa']));

        $html = view('pdf.dashboard_chart_report', [
            'stats' => [
                'activity' => ['total' => 1, 'this_month' => 1],
                'documents' => ['total_buku_tracked' => 19, 'buku_terisi' => 4, 'buku_belum_terisi' => 15],
            ],
            'charts' => [
                'activity' => [
                    'monthly' => ['labels' => ['Jan 2026'], 'values' => [1]],
                    'level' => ['labels' => ['Desa', 'Kecamatan'], 'values' => [1, 0]],
                ],
                'documents' => [
                    'coverage_per_buku' => ['labels' => ['activities'], 'values' => [1]],
                    'coverage_per_lampiran' => ['labels' => ['4.13'], 'values' => [1]],
                ],
            ],
            'filters' => [
                'mode' => 'all',
                'level' => 'all',
                'sub_level' => 'all',
                'section1_month' => 'all',
                'tahun_anggaran' => (string) self::ACTIVE_BUDGET_YEAR,
            ],
            'printedBy' => $user,
            'printedAt' => now(),
        ])->render();

        $this->assertStringContainsString('Tahun Anggaran: 2026', $html);
    }
}
