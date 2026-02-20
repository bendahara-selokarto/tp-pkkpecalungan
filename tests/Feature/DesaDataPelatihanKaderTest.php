<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataPelatihanKaderTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_data_pelatihan_kader_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-001',
            'nama_lengkap_kader' => 'Siti A',
            'tanggal_masuk_tp_pkk' => '2021',
            'jabatan_fungsi' => 'Sekretaris',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Administrasi',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-002',
            'nama_lengkap_kader' => 'Siti B',
            'tanggal_masuk_tp_pkk' => '2020',
            'jabatan_fungsi' => 'Bendahara',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Kearsipan',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Tidak',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-pelatihan-kader');

        $response->assertOk();
        $response->assertSee('REG-001');
        $response->assertDontSee('REG-002');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_pelatihan_kader(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-pelatihan-kader', [
            'nomor_registrasi' => 'REG-010',
            'nama_lengkap_kader' => 'Nur Aini',
            'tanggal_masuk_tp_pkk' => '12/05/2020',
            'jabatan_fungsi' => 'Pokja II',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Kader Dasar',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kecamatan',
            'status_sertifikat' => 'Bersertifikat',
        ])->assertStatus(302);

        $dataPelatihan = DataPelatihanKader::where('nomor_registrasi', 'REG-010')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-pelatihan-kader.update', $dataPelatihan->id), [
            'nomor_registrasi' => 'REG-010',
            'nama_lengkap_kader' => 'Nur Aini',
            'tanggal_masuk_tp_pkk' => '12/05/2020',
            'jabatan_fungsi' => 'Pokja II',
            'nomor_urut_pelatihan' => 2,
            'judul_pelatihan' => 'Pelatihan Kader Dasar',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kecamatan',
            'status_sertifikat' => 'Tidak',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_pelatihan_kaders', [
            'id' => $dataPelatihan->id,
            'nomor_urut_pelatihan' => 2,
            'status_sertifikat' => 'Tidak',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-pelatihan-kader.destroy', $dataPelatihan->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_pelatihan_kaders', ['id' => $dataPelatihan->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_pelatihan_kader_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-pelatihan-kader');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_pelatihan_kader_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/data-pelatihan-kader');

        $response->assertStatus(403);
    }
}
