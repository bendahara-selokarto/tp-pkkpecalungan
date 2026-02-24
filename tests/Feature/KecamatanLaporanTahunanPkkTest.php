<?php

namespace Tests\Feature;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertSee('Laporan Kecamatan A');
        $response->assertDontSee('Laporan Kecamatan B');
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

