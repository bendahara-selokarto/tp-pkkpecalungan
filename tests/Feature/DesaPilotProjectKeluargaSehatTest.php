<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaPilotProjectKeluargaSehatTest extends TestCase
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
    public function admin_desa_dapat_crud_laporan_pilot_project_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->get('/desa/pilot-project-keluarga-sehat')->assertOk();

        $this->actingAs($adminDesa)->post('/desa/pilot-project-keluarga-sehat', [
            'judul_laporan' => 'Laporan Pilot Project Desa A',
            'dasar_hukum' => 'Peraturan contoh',
            'pendahuluan' => 'Pendahuluan',
            'maksud_tujuan' => 'Tujuan',
            'pelaksanaan' => 'Pelaksanaan',
            'dokumentasi' => 'Dokumentasi',
            'penutup' => 'Penutup',
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'values' => [
                [
                    'section' => 'data_dukung',
                    'cluster_code' => 'support',
                    'indicator_code' => 'jumlah_penduduk',
                    'indicator_label' => 'Jumlah penduduk',
                    'year' => 2021,
                    'semester' => 'I',
                    'value' => 120,
                    'evaluation_note' => 'Baseline',
                    'keterangan_note' => 'Data sumber BPS',
                    'sort_order' => 1,
                ],
            ],
        ])->assertStatus(302);

        $report = PilotProjectKeluargaSehatReport::query()
            ->where('area_id', $this->desaA->id)
            ->firstOrFail();

        $this->assertDatabaseHas('pilot_project_keluarga_sehat_reports', [
            'id' => $report->id,
            'judul_laporan' => 'Laporan Pilot Project Desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
        ]);

        $this->assertDatabaseHas('pilot_project_keluarga_sehat_values', [
            'report_id' => $report->id,
            'cluster_code' => 'SUPPORT',
            'semester' => 1,
            'value' => 120,
            'evaluation_note' => 'Baseline',
            'keterangan_note' => 'Data sumber BPS',
        ]);

        $this->actingAs($adminDesa)->put(route('desa.pilot-project-keluarga-sehat.update', $report->id), [
            'judul_laporan' => 'Laporan Pilot Project Desa A',
            'dasar_hukum' => 'Peraturan contoh',
            'pendahuluan' => 'Pendahuluan',
            'maksud_tujuan' => 'Tujuan',
            'pelaksanaan' => 'Pelaksanaan revisi',
            'dokumentasi' => 'Dokumentasi',
            'penutup' => 'Penutup',
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'values' => [
                [
                    'section' => 'data_dukung',
                    'cluster_code' => 'SUPPORT',
                    'indicator_code' => 'jumlah_penduduk',
                    'indicator_label' => 'Jumlah penduduk',
                    'year' => 2021,
                    'semester' => 'II',
                    'value' => 145,
                    'evaluation_note' => 'Semester dua',
                    'keterangan_note' => 'Update semester dua',
                    'sort_order' => 1,
                ],
            ],
        ])->assertStatus(302);

        $this->assertDatabaseHas('pilot_project_keluarga_sehat_values', [
            'report_id' => $report->id,
            'indicator_code' => 'jumlah_penduduk',
            'semester' => 2,
            'value' => 145,
            'evaluation_note' => 'Semester dua',
            'keterangan_note' => 'Update semester dua',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.pilot-project-keluarga-sehat.destroy', $report->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('pilot_project_keluarga_sehat_reports', [
            'id' => $report->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/pilot-project-keluarga-sehat');

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

        $response = $this->actingAs($invalidUser)->get('/desa/pilot-project-keluarga-sehat');

        $response->assertStatus(403);
    }
}
