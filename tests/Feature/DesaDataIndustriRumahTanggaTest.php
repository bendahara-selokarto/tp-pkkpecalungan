<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataIndustriRumahTanggaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_data_industri_rumah_tangga_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Ayam',
            'jumlah_komoditi' => '20 ekor',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Sandang',
            'komoditi' => 'Lele',
            'jumlah_komoditi' => '15 kolam',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-industri-rumah-tangga');

        $response->assertOk();
        $response->assertSee('Pangan');
        $response->assertDontSee('Sandang');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_industri_rumah_tangga(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-industri-rumah-tangga', [
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Ayam Kampung',
            'jumlah_komoditi' => '12 ekor',
        ])->assertStatus(302);

        $DataIndustriRumahTangga = DataIndustriRumahTangga::where('kategori_jenis_industri', 'Pangan')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-industri-rumah-tangga.update', $DataIndustriRumahTangga->id), [
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Ayam Kampung',
            'jumlah_komoditi' => '14 ekor',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_industri_rumah_tanggas', [
            'id' => $DataIndustriRumahTangga->id,
            'jumlah_komoditi' => '14 ekor',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-industri-rumah-tangga.destroy', $DataIndustriRumahTangga->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_industri_rumah_tanggas', ['id' => $DataIndustriRumahTangga->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_industri_rumah_tangga_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-industri-rumah-tangga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_industri_rumah_tangga_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/data-industri-rumah-tangga');

        $response->assertStatus(403);
    }
}




