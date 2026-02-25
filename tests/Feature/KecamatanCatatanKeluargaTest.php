<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanCatatanKeluargaTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_catatan_keluarga_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataWarga::create([
            'dasawisma' => 'Mawar 1',
            'nama_kepala_keluarga' => 'Kepala Kec A',
            'alamat' => 'Alamat A',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 3,
            'keterangan' => 'Keterangan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataWarga::create([
            'dasawisma' => 'Mawar 2',
            'nama_kepala_keluarga' => 'Kepala Kec B',
            'alamat' => 'Alamat B',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => 'Keterangan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Arisan',
            'aktivitas' => true,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/catatan-keluarga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/CatatanKeluarga/Index')
                ->has('catatanKeluargaItems.data', 1)
                ->where('catatanKeluargaItems.data.0.nama_kepala_rumah_tangga', 'Kepala Kec A')
                ->where('catatanKeluargaItems.data.0.arisan', 'Ya')
                ->where('catatanKeluargaItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_catatan_keluarga_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            DataWarga::create([
                'dasawisma' => 'Mawar ' . $index,
                'nama_kepala_keluarga' => 'Kepala Kec ' . $index,
                'alamat' => 'Alamat ' . $index,
                'jumlah_warga_laki_laki' => 1,
                'jumlah_warga_perempuan' => 2,
                'keterangan' => 'Keterangan ' . $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        DataWarga::create([
            'dasawisma' => 'Bocor',
            'nama_kepala_keluarga' => 'Kepala Kec Bocor',
            'alamat' => 'Alamat Bocor',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 1,
            'keterangan' => 'Bocor',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/catatan-keluarga?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kepala Kec Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/CatatanKeluarga/Index')
                ->has('catatanKeluargaItems.data', 1)
                ->where('catatanKeluargaItems.current_page', 2)
                ->where('catatanKeluargaItems.per_page', 10)
                ->where('catatanKeluargaItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_catatan_keluarga_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataWarga::create([
            'dasawisma' => 'Mawar 1',
            'nama_kepala_keluarga' => 'Kepala Kec Default',
            'alamat' => 'Alamat Default',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => 'Default Per Page',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/catatan-keluarga?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/CatatanKeluarga/Index')
                ->where('filters.per_page', 10)
                ->where('catatanKeluargaItems.per_page', 10);
        });
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_catatan_keluarga_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/catatan-keluarga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_catatan_keluarga_kecamatan(): void
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

        $response = $this->actingAs($userStale)->get('/kecamatan/catatan-keluarga');

        $response->assertStatus(403);
    }
}
