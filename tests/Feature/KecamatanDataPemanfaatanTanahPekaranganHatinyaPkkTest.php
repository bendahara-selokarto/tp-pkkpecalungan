<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            'kategori_pemanfaatan' => 'Sejahtera III',
            'jumlah_kk_memanfaatkan' => 30,
            'keterangan' => 'Rekap kecamatan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan' => 'Sejahtera III Plus',
            'jumlah_kk_memanfaatkan' => 22,
            'keterangan' => 'Rekap kecamatan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/data-pemanfaatan-tanah-pekarangan-hatinya-pkk');

        $response->assertOk();
        $response->assertSee('Sejahtera III');
        $response->assertDontSee('Sejahtera III Plus');
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
            'kategori_pemanfaatan' => 'Pra Sejahtera',
            'jumlah_kk_memanfaatkan' => 18,
            'keterangan' => 'Data luar',
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


