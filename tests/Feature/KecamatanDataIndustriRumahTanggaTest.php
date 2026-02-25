<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDataIndustriRumahTanggaTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_data_industri_rumah_tangga_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Kambing',
            'jumlah_komoditi' => '30 ekor',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Sandang',
            'komoditi' => 'Nila',
            'jumlah_komoditi' => '22 kolam',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-industri-rumah-tangga');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataIndustriRumahTangga/Index')
                ->has('dataIndustriRumahTanggaItems.data', 1)
                ->where('dataIndustriRumahTanggaItems.data.0.kategori_jenis_industri', 'Pangan')
                ->where('dataIndustriRumahTanggaItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_data_industri_rumah_tangga_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            DataIndustriRumahTangga::create([
                'kategori_jenis_industri' => 'Pangan',
                'komoditi' => 'Komoditi ' . $index,
                'jumlah_komoditi' => $index . ' unit',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Sandang',
            'komoditi' => 'Komoditi Bocor',
            'jumlah_komoditi' => '1 unit',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-industri-rumah-tangga?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Komoditi Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataIndustriRumahTangga/Index')
                ->has('dataIndustriRumahTanggaItems.data', 1)
                ->where('dataIndustriRumahTanggaItems.current_page', 2)
                ->where('dataIndustriRumahTanggaItems.per_page', 10)
                ->where('dataIndustriRumahTanggaItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_data_industri_rumah_tangga_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Komoditi Default',
            'jumlah_komoditi' => '2 unit',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-industri-rumah-tangga?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataIndustriRumahTangga/Index')
                ->where('filters.per_page', 10)
                ->where('dataIndustriRumahTanggaItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_data_industri_rumah_tangga_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $DataIndustriRumahTanggaLuar = DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Lain-lain',
            'komoditi' => 'Hidroponik',
            'jumlah_komoditi' => '18 instalasi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.data-industri-rumah-tangga.show', $DataIndustriRumahTanggaLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_data_industri_rumah_tangga_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/data-industri-rumah-tangga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_industri_rumah_tangga_kecamatan(): void
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

        $response = $this->actingAs($userStale)->get('/kecamatan/data-industri-rumah-tangga');

        $response->assertStatus(403);
    }
}



