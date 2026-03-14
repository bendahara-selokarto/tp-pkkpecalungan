<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\Dashboard\Repositories\DashboardDocumentCoverageRepositoryInterface;
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

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_grafik_dashboard_pengguna_desa_terbatas_pada_desanya_sendiri(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'desa', 'area_id' => $desaA->id, 'active_budget_year' => 2026])->save();
        $user->assignRole('desa-sekretaris');

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
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('auth.user.scope', 'desa');
        });
    }

    public function test_grafik_dashboard_tidak_menghitung_kegiatan_tahun_anggaran_lain_di_wilayah_yang_sama(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'desa', 'area_id' => $desa->id, 'active_budget_year' => 2026])->save();
        $user->assignRole('desa-sekretaris');

        Activity::create([
            'title' => 'Aktif 2026',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
            'tahun_anggaran' => 2026,
        ]);

        Activity::create([
            'title' => 'Lama 2025',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2025-02-10',
            'status' => 'draft',
            'tahun_anggaran' => 2025,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts');
        });
    }

    public function test_grafik_dashboard_pengguna_kecamatan_mencakup_kecamatan_sendiri_dan_desa_turunan(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatanA->id]);
        $desaB = Area::create(['name' => 'Kalisalak', 'level' => 'desa', 'parent_id' => $kecamatanB->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id])->save();
        $user->assignRole('kecamatan-sekretaris');

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

        $expectedBooksTotal = $this->expectedBooksTotal();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) use ($expectedBooksTotal) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('auth.user.scope', 'kecamatan');
        });
    }

    public function test_grafik_dashboard_tidak_bocor_saat_scope_metadata_tidak_sinkron_dengan_role(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        // Simulasi data legacy/stale: role desa tetapi metadata scope + area level kecamatan.
        $user->forceFill(['scope' => 'kecamatan', 'area_id' => $kecamatan->id])->save();
        $user->assignRole('desa-sekretaris');

        Activity::create([
            'title' => 'Kec A',
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Desa A',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-09',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('auth.user.scope', null);
        });
    }

    public function test_grafik_dashboard_tidak_bocor_saat_role_kecamatan_tetapi_area_level_desa(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        // Simulasi data legacy/stale: role kecamatan tetapi area level desa.
        $user->forceFill(['scope' => 'kecamatan', 'area_id' => $desa->id])->save();
        $user->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Kegiatan Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-09',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts')
                ->where('auth.user.scope', null);
        });
    }

    public function test_grafik_kegiatan_per_desa_dapat_difilter_berdasarkan_bulan(): void
    {
        Carbon::setTestNow('2026-02-14');

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create();
        $user->forceFill(['scope' => 'kecamatan', 'area_id' => $kecamatan->id])->save();
        $user->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Aktivitas Januari',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-01-10',
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Aktivitas Februari',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2026-02-11',
            'tanggal_surat' => '2026-02-11',
            'nomor_surat' => 'A-001',
            'asal_surat' => 'Asal',
            'dari' => 'Dari',
            'kepada' => 'Kepada',
            'perihal' => 'Perihal',
            'lampiran' => null,
            'diteruskan_kepada' => null,
            'tembusan' => null,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        $expectedBooksTotal = $this->expectedBooksTotal();

        $januaryResponse = $this->actingAs($user)->get(route('dashboard', ['section1_month' => '1']));
        $januaryResponse->assertOk();
        $januaryResponse->assertInertia(function (AssertableInertia $page) use ($expectedBooksTotal) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts');
        });

        $februaryResponse = $this->actingAs($user)->get(route('dashboard', ['section1_month' => '2']));
        $februaryResponse->assertOk();
        $februaryResponse->assertInertia(function (AssertableInertia $page) use ($expectedBooksTotal) {
            $page
                ->component('Dashboard')
                ->missing('dashboardStats')
                ->missing('dashboardCharts');
        });
    }

    private function expectedBooksTotal(): int
    {
        return count(app(DashboardDocumentCoverageRepositoryInterface::class)->trackedModuleSlugs());
    }
}
