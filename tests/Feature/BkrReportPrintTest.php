<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BkrReportPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_bkr_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        Bkr::create([
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Mawar',
            'no_tgl_sk' => '01/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Siti Aminah',
            'jumlah_anggota' => 20,
            'kegiatan' => 'Pertemuan kader',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.bkr.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_bkr_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        Bkr::create([
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Melati',
            'no_tgl_sk' => '02/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Rina Wati',
            'jumlah_anggota' => 28,
            'kegiatan' => 'Pendampingan keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.bkr.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_bkr_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.bkr.report'));

        $response->assertStatus(403);
    }
}

