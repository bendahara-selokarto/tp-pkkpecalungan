<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDataPemanfaatanTanahPekaranganHatinyaPkkTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Kambing',
            'jumlah_komoditi' => '30 ekor',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Perikanan',
            'komoditi' => 'Nila',
            'jumlah_komoditi' => '22 kolam',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->has('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data', 1)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data.0.kategori_pemanfaatan_lahan', 'Peternakan')
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            DataPemanfaatanTanahPekaranganHatinyaPkk::create([
                'kategori_pemanfaatan_lahan' => 'Peternakan',
                'komoditi' => 'Komoditi ' . $index,
                'jumlah_komoditi' => $index . ' unit',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Perikanan',
            'komoditi' => 'Komoditi Bocor',
            'jumlah_komoditi' => '1 unit',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Komoditi Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->has('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data', 1)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.current_page', 2)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.per_page', 10)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Komoditi Default',
            'jumlah_komoditi' => '2 unit',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->where('filters.per_page', 10)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $DataPemanfaatanTanahPekaranganHatinyaPkkLuar = DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Lainnya',
            'komoditi' => 'Hidroponik',
            'jumlah_komoditi' => '18 instalasi',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.show', $DataPemanfaatanTanahPekaranganHatinyaPkkLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan(): void
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

        $response = $this->actingAs($userStale)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertStatus(403);
    }
}

