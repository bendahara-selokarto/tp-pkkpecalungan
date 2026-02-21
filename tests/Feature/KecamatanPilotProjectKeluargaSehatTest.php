<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanPilotProjectKeluargaSehatTest extends TestCase
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

        PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Kecamatan A',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Kecamatan B',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/pilot-project-keluarga-sehat');

        $response->assertOk();
        $response->assertSee('Laporan Kecamatan A');
        $response->assertDontSee('Laporan Kecamatan B');
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/pilot-project-keluarga-sehat');

        $response->assertStatus(403);
    }
}
