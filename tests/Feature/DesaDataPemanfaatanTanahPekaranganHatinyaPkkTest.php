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

class DesaDataPemanfaatanTanahPekaranganHatinyaPkkTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'desa-pokja-iii']);
        Role::create(['name' => 'kecamatan-pokja-iii']);

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
    public function admin_desa_dapat_melihat_daftar_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-pokja-iii');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Ayam',
            'jumlah_komoditi' => '20 ekor',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Perikanan',
            'komoditi' => 'Lele',
            'jumlah_komoditi' => '15 kolam',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->has('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data', 1)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data.0.kategori_pemanfaatan_lahan', 'Peternakan')
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-pokja-iii');

        for ($index = 1; $index <= 12; $index++) {
            DataPemanfaatanTanahPekaranganHatinyaPkk::create([
                'kategori_pemanfaatan_lahan' => 'Peternakan',
                'komoditi' => 'Komoditi '.$index,
                'jumlah_komoditi' => $index.' unit',
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Perikanan',
            'komoditi' => 'Komoditi Bocor',
            'jumlah_komoditi' => '1 unit',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Komoditi Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->has('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data', 2)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.current_page', 2)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.per_page', 10)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-pokja-iii');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Komoditi Default',
            'jumlah_komoditi' => '2 unit',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->where('filters.per_page', 10)
                ->where('dataPemanfaatanTanahPekaranganHatinyaPkkItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_pemanfaatan_tanah_pekarangan_hatinya_pkk(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('desa-pokja-iii');

        $this->actingAs($adminDesa)->post('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk', [
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Ayam Kampung',
            'jumlah_komoditi' => '12 ekor',
        ])->assertStatus(302);

        $DataPemanfaatanTanahPekaranganHatinyaPkk = DataPemanfaatanTanahPekaranganHatinyaPkk::where('kategori_pemanfaatan_lahan', 'Peternakan')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.update', $DataPemanfaatanTanahPekaranganHatinyaPkk->id), [
            'kategori_pemanfaatan_lahan' => 'Peternakan',
            'komoditi' => 'Ayam Kampung',
            'jumlah_komoditi' => '14 ekor',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_pemanfaatan_tanah_pekarangan_hatinya_pkks', [
            'id' => $DataPemanfaatanTanahPekaranganHatinyaPkk->id,
            'jumlah_komoditi' => '14 ekor',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.destroy', $DataPemanfaatanTanahPekaranganHatinyaPkk->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_pemanfaatan_tanah_pekarangan_hatinya_pkks', ['id' => $DataPemanfaatanTanahPekaranganHatinyaPkk->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-iii');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('desa-pokja-iii');

        $response = $this->actingAs($userStale)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_desa_hanya_melihat_data_hatinya_pkk_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-iii');

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'warung_hidup',
            'komoditi' => 'Cabai',
            'jumlah_komoditi' => '18 rumpun',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan_lahan' => 'toga',
            'komoditi' => 'Jahe Lama',
            'jumlah_komoditi' => '12 rumpun',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertOk();
        $response->assertDontSee('Jahe Lama');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Index')
                ->has('dataPemanfaatanTanahPekaranganHatinyaPkkItems.data', 1)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }
}
