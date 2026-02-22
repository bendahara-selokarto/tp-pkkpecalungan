<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPilotProjectNaskahPelaporanTest extends TestCase
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
    public function admin_kecamatan_hanya_melihat_naskah_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Kecamatan A',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'A',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Kecamatan B',
            'dasar_pelaksanaan' => 'B',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/pilot-project-naskah-pelaporan');

        $response->assertOk();
        $response->assertSee('Naskah Kecamatan A');
        $response->assertDontSee('Naskah Kecamatan B');
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_naskah_pelaporan(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/pilot-project-naskah-pelaporan', [
            'judul_laporan' => 'Naskah Kecamatan A',
            'surat_kepada' => 'Tim Penggerak PKK Kabupaten',
            'surat_dari' => 'Tim Penggerak PKK Kecamatan Pecalungan',
            'surat_tanggal' => '2026-02-22',
            'dasar_pelaksanaan' => 'Dasar pelaksanaan awal',
            'pendahuluan' => 'Pendahuluan awal',
            'pelaksanaan_1' => 'Pelaksanaan 1',
            'pelaksanaan_2' => 'Pelaksanaan 2',
            'pelaksanaan_3' => 'Pelaksanaan 3',
            'pelaksanaan_4' => 'Pelaksanaan 4',
            'pelaksanaan_5' => 'Pelaksanaan 5',
            'penutup' => 'Penutup awal',
        ])->assertStatus(302);

        $report = PilotProjectNaskahPelaporanReport::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('judul_laporan', 'Naskah Kecamatan A')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.pilot-project-naskah-pelaporan.update', $report->id), [
            'judul_laporan' => 'Naskah Kecamatan A Revisi',
            'surat_kepada' => 'Tim Penggerak PKK Kabupaten',
            'surat_dari' => 'Tim Penggerak PKK Kecamatan Pecalungan',
            'surat_tanggal' => '2026-02-23',
            'dasar_pelaksanaan' => 'Dasar pelaksanaan revisi',
            'pendahuluan' => 'Pendahuluan revisi',
            'pelaksanaan_1' => 'Pelaksanaan 1 revisi',
            'pelaksanaan_2' => 'Pelaksanaan 2 revisi',
            'pelaksanaan_3' => 'Pelaksanaan 3 revisi',
            'pelaksanaan_4' => 'Pelaksanaan 4 revisi',
            'pelaksanaan_5' => 'Pelaksanaan 5 revisi',
            'penutup' => 'Penutup revisi',
        ])->assertStatus(302);

        $this->assertDatabaseHas('pilot_project_naskah_pelaporan_reports', [
            'id' => $report->id,
            'judul_laporan' => 'Naskah Kecamatan A Revisi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_naskah_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/pilot-project-naskah-pelaporan');

        $response->assertStatus(403);
    }
}
