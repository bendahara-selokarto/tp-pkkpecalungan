<?php

namespace Tests\Feature;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkEntry;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaLaporanTahunanPkkTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_hanya_melihat_laporan_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Desa A',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Desa B',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/laporan-tahunan-pkk');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->has('reports.data', 1)
                ->where('reports.data.0.judul_laporan', 'Laporan Desa A')
                ->where('reports.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_laporan_tahunan_pkk_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            LaporanTahunanPkkReport::create([
                'judul_laporan' => 'Laporan Desa ' . $index,
                'tahun_laporan' => 2010 + $index,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Bocor',
            'tahun_laporan' => 2040,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/laporan-tahunan-pkk?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Laporan Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->has('reports.data', 2)
                ->where('reports.current_page', 2)
                ->where('reports.per_page', 10)
                ->where('reports.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_laporan_tahunan_pkk_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Default',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/laporan-tahunan-pkk?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('LaporanTahunanPkk/Index')
                ->where('filters.per_page', 10)
                ->where('reports.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_crud_laporan_tahunan_dengan_isian_pelengkap(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/laporan-tahunan-pkk', [
            'judul_laporan' => 'Laporan Tahunan Desa A',
            'tahun_laporan' => 2025,
            'pendahuluan' => 'Pendahuluan contoh',
            'keberhasilan' => 'Keberhasilan contoh',
            'hambatan' => 'Hambatan contoh',
            'kesimpulan' => 'Kesimpulan contoh',
            'penutup' => 'Penutup contoh',
            'disusun_oleh' => 'Tim Penggerak PKK Desa Gombong',
            'jabatan_penanda_tangan' => 'Ketua TP. PKK Desa',
            'nama_penanda_tangan' => 'Siti Aisyah',
            'manual_entries' => [
                [
                    'bidang' => 'sekretariat',
                    'activity_date' => '2025-02-10',
                    'description' => 'Rapat koordinasi tahunan',
                ],
            ],
        ])->assertStatus(302);

        $report = LaporanTahunanPkkReport::query()
            ->where('area_id', $this->desaA->id)
            ->where('tahun_laporan', 2025)
            ->firstOrFail();

        $this->assertDatabaseHas('laporan_tahunan_pkk_reports', [
            'id' => $report->id,
            'judul_laporan' => 'Laporan Tahunan Desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
        ]);

        $this->assertDatabaseHas('laporan_tahunan_pkk_entries', [
            'report_id' => $report->id,
            'bidang' => 'sekretariat',
            'description' => 'Rapat koordinasi tahunan',
        ]);

        $this->actingAs($adminDesa)->put(route('desa.laporan-tahunan-pkk.update', $report->id), [
            'judul_laporan' => 'Laporan Tahunan Desa A Revisi',
            'tahun_laporan' => 2025,
            'pendahuluan' => 'Pendahuluan revisi',
            'keberhasilan' => 'Keberhasilan revisi',
            'hambatan' => 'Hambatan revisi',
            'kesimpulan' => 'Kesimpulan revisi',
            'penutup' => 'Penutup revisi',
            'disusun_oleh' => 'Tim Penggerak PKK Desa Gombong',
            'jabatan_penanda_tangan' => 'Ketua TP. PKK Desa',
            'nama_penanda_tangan' => 'Siti Aisyah',
            'manual_entries' => [
                [
                    'bidang' => 'pokja-i',
                    'activity_date' => '2025-03-12',
                    'description' => 'Pembinaan kader Pokja I',
                ],
            ],
        ])->assertStatus(302);

        $this->assertDatabaseMissing('laporan_tahunan_pkk_entries', [
            'report_id' => $report->id,
            'description' => 'Rapat koordinasi tahunan',
        ]);

        $this->assertDatabaseHas('laporan_tahunan_pkk_entries', [
            'report_id' => $report->id,
            'bidang' => 'pokja-i',
            'description' => 'Pembinaan kader Pokja I',
        ]);

        $this->actingAs($adminDesa)
            ->delete(route('desa.laporan-tahunan-pkk.destroy', $report->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('laporan_tahunan_pkk_reports', [
            'id' => $report->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_laporan_tahunan_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/laporan-tahunan-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_desa_dengan_level_area_tidak_sinkron_ditolak(): void
    {
        $invalidUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $invalidUser->assignRole('admin-desa');

        $response = $this->actingAs($invalidUser)->get('/desa/laporan-tahunan-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function update_dengan_tahun_laporan_tetap_memperbarui_isian_pelengkap(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Awal',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        LaporanTahunanPkkEntry::create([
            'report_id' => $report->id,
            'bidang' => 'sekretariat',
            'activity_date' => '2025-01-10',
            'description' => 'Data lama',
            'entry_source' => 'manual',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.laporan-tahunan-pkk.update', $report->id), [
            'judul_laporan' => 'Laporan Awal',
            'tahun_laporan' => 2025,
            'manual_entries' => [
                [
                    'bidang' => 'pokja-ii',
                    'activity_date' => '2025-06-20',
                    'description' => 'Data baru',
                ],
            ],
        ])->assertStatus(302);

        $this->assertDatabaseMissing('laporan_tahunan_pkk_entries', [
            'report_id' => $report->id,
            'description' => 'Data lama',
        ]);

        $this->assertDatabaseHas('laporan_tahunan_pkk_entries', [
            'report_id' => $report->id,
            'description' => 'Data baru',
            'bidang' => 'pokja-ii',
        ]);
    }
}
