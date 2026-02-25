<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataKegiatanWargaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_data_kegiatan_warga_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataKegiatanWarga::create([
            'kegiatan' => 'Penghayatan dan Pengamalan Pancasila',
            'aktivitas' => true,
            'keterangan' => 'Pembinaan rutin RT 01',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Bersih lingkungan',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-kegiatan-warga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataKegiatanWarga/Index')
                ->has('dataKegiatanWargaItems.data', 1)
                ->where('dataKegiatanWargaItems.data.0.kegiatan', 'Penghayatan dan Pengamalan Pancasila')
                ->where('dataKegiatanWargaItems.data.0.aktivitas_label', 'Ya')
                ->where('dataKegiatanWargaItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_data_kegiatan_warga_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            DataKegiatanWarga::create([
                'kegiatan' => 'Kegiatan Desa ' . $index,
                'aktivitas' => true,
                'keterangan' => 'Keterangan ' . $index,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        DataKegiatanWarga::create([
            'kegiatan' => 'Kegiatan Bocor',
            'aktivitas' => true,
            'keterangan' => 'Bocor',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-kegiatan-warga?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kegiatan Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataKegiatanWarga/Index')
                ->has('dataKegiatanWargaItems.data', 2)
                ->where('dataKegiatanWargaItems.current_page', 2)
                ->where('dataKegiatanWargaItems.per_page', 10)
                ->where('dataKegiatanWargaItems.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_data_kegiatan_warga_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataKegiatanWarga::create([
            'kegiatan' => 'Kegiatan Default',
            'aktivitas' => true,
            'keterangan' => 'Default',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-kegiatan-warga?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataKegiatanWarga/Index')
                ->where('filters.per_page', 10)
                ->where('dataKegiatanWargaItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_kegiatan_warga(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-kegiatan-warga', [
            'kegiatan' => 'Rukun Kematian',
            'aktivitas' => true,
            'keterangan' => 'Takziyah warga',
        ])->assertStatus(302);

        $dataKegiatanWarga = DataKegiatanWarga::where('kegiatan', 'Rukun Kematian')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-kegiatan-warga.update', $dataKegiatanWarga->id), [
            'kegiatan' => 'Rukun Kematian',
            'aktivitas' => false,
            'keterangan' => 'Tidak ada kegiatan bulan ini',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_kegiatan_wargas', [
            'id' => $dataKegiatanWarga->id,
            'aktivitas' => false,
            'keterangan' => 'Tidak ada kegiatan bulan ini',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-kegiatan-warga.destroy', $dataKegiatanWarga->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_kegiatan_wargas', ['id' => $dataKegiatanWarga->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_kegiatan_warga_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-kegiatan-warga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_kegiatan_warga_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/data-kegiatan-warga');

        $response->assertStatus(403);
    }
}
