<?php

namespace Tests\Feature;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanLaporanTahunanPkkTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
    }

    #[Test]
    public function admin_kecamatan_hanya_melihat_laporan_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Kecamatan A',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Kecamatan B',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/laporan-tahunan-pkk');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->has('reports.data', 1)
                ->where('reports.data.0.judul_laporan', 'Laporan Kecamatan A')
                ->where('reports.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_laporan_tahunan_pkk_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            LaporanTahunanPkkReport::create([
                'judul_laporan' => 'Laporan Kecamatan ' . $index,
                'tahun_laporan' => 2010 + $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Bocor',
            'tahun_laporan' => 2040,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/laporan-tahunan-pkk?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Laporan Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->has('reports.data', 1)
                ->where('reports.current_page', 2)
                ->where('reports.per_page', 10)
                ->where('reports.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_laporan_tahunan_pkk_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Default',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/laporan-tahunan-pkk?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->where('filters.per_page', 10)
                ->where('reports.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_laporan_tahunan(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/laporan-tahunan-pkk', [
            'judul_laporan' => 'Laporan Kecamatan A',
            'tahun_laporan' => 2025,
            'pendahuluan' => 'Pendahuluan awal',
            'manual_entries' => [
                [
                    'bidang' => 'sekretariat',
                    'activity_date' => '2025-02-01',
                    'description' => 'Koordinasi sekretariat',
                ],
            ],
        ])->assertStatus(302);

        $report = LaporanTahunanPkkReport::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('tahun_laporan', 2025)
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.laporan-tahunan-pkk.update', $report->id), [
            'judul_laporan' => 'Laporan Kecamatan A Revisi',
            'tahun_laporan' => 2025,
            'pendahuluan' => 'Pendahuluan revisi',
            'manual_entries' => [
                [
                    'bidang' => 'pokja-iv',
                    'activity_date' => '2025-03-15',
                    'description' => 'Pembinaan Posyandu',
                ],
            ],
        ])->assertStatus(302);

        $this->assertDatabaseHas('laporan_tahunan_pkk_reports', [
            'id' => $report->id,
            'judul_laporan' => 'Laporan Kecamatan A Revisi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_laporan_tahunan_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/laporan-tahunan-pkk');

        $response->assertStatus(403);
    }
}
