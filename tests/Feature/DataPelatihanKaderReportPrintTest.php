<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataPelatihanKaderReportPrintTest extends TestCase
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

    public function test_admin_desa_dapat_mencetak_laporan_pdf_data_pelatihan_kader_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-DESA-1',
            'nama_lengkap_kader' => 'Kader Desa',
            'tanggal_masuk_tp_pkk' => '2020',
            'jabatan_fungsi' => 'Sekretaris',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Administrasi',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kecamatan',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.data-pelatihan-kader.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_data_pelatihan_kader_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-KEC-1',
            'nama_lengkap_kader' => 'Kader Kecamatan',
            'tanggal_masuk_tp_pkk' => '2018',
            'jabatan_fungsi' => 'Pokja I',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Kepemimpinan',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Tidak',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.data-pelatihan-kader.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_data_pelatihan_kader_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.data-pelatihan-kader.report'));

        $response->assertStatus(403);
    }
}
