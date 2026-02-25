<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDataPelatihanKaderTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_data_pelatihan_kader_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-KEC-01',
            'nama_lengkap_kader' => 'Kader A',
            'tanggal_masuk_tp_pkk' => '2019',
            'jabatan_fungsi' => 'Sekretaris',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Penguatan Kader',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-KEC-02',
            'nama_lengkap_kader' => 'Kader B',
            'tanggal_masuk_tp_pkk' => '2018',
            'jabatan_fungsi' => 'Bendahara',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Arsip',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2023,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Tidak',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pelatihan-kader');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPelatihanKader/Index')
                ->has('dataPelatihanKaderItems.data', 1)
                ->where('dataPelatihanKaderItems.data.0.nomor_registrasi', 'REG-KEC-01')
                ->where('dataPelatihanKaderItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_data_pelatihan_kader_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            DataPelatihanKader::create([
                'nomor_registrasi' => 'REG-KEC-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'nama_lengkap_kader' => 'Kader Kec ' . $index,
                'tanggal_masuk_tp_pkk' => '2019',
                'jabatan_fungsi' => 'Pokja I',
                'nomor_urut_pelatihan' => 1,
                'judul_pelatihan' => 'Pelatihan ' . $index,
                'jenis_kriteria_kaderisasi' => 'Lanjutan',
                'tahun_penyelenggaraan' => 2024,
                'institusi_penyelenggara' => 'TP PKK Kabupaten',
                'status_sertifikat' => 'Bersertifikat',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-BOCOR',
            'nama_lengkap_kader' => 'Kader Bocor',
            'tanggal_masuk_tp_pkk' => '2019',
            'jabatan_fungsi' => 'Pokja II',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Bocor',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Tidak',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pelatihan-kader?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('REG-BOCOR');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPelatihanKader/Index')
                ->has('dataPelatihanKaderItems.data', 1)
                ->where('dataPelatihanKaderItems.current_page', 2)
                ->where('dataPelatihanKaderItems.per_page', 10)
                ->where('dataPelatihanKaderItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_data_pelatihan_kader_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-DEFAULT',
            'nama_lengkap_kader' => 'Kader Default',
            'tanggal_masuk_tp_pkk' => '2019',
            'jabatan_fungsi' => 'Pokja I',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Default',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pelatihan-kader?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPelatihanKader/Index')
                ->where('filters.per_page', 10)
                ->where('dataPelatihanKaderItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_data_pelatihan_kader_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $dataPelatihanLuar = DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-OUT-1',
            'nama_lengkap_kader' => 'Kader Luar',
            'tanggal_masuk_tp_pkk' => '2017',
            'jabatan_fungsi' => 'Pokja III',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Komunikasi',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Provinsi',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.data-pelatihan-kader.show', $dataPelatihanLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_data_pelatihan_kader_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/data-pelatihan-kader');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_pelatihan_kader_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $userStale = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $userStale->assignRole('admin-kecamatan');

        $response = $this->actingAs($userStale)->get('/kecamatan/data-pelatihan-kader');

        $response->assertStatus(403);
    }
}
