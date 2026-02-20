<?php

namespace Tests\Feature;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaWarungPkkTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_warung_pkk_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'komoditi' => 'Beras',
            'kategori' => 'Pangan',
            'volume' => '100 kg',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Melati',
            'nama_pengelola' => 'Rina Wati',
            'komoditi' => 'Minyak goreng',
            'kategori' => 'Pangan',
            'volume' => '80 liter',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/warung-pkk');

        $response->assertOk();
        $response->assertSee('Warung PKK Mawar');
        $response->assertDontSee('Warung PKK Melati');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_warung_pkk(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/warung-pkk', [
            'nama_warung_pkk' => 'Warung PKK Anggrek',
            'nama_pengelola' => 'Nur Aini',
            'komoditi' => 'Telur',
            'kategori' => 'Pangan',
            'volume' => '25 kg',
        ])->assertStatus(302);

        $warungPkk = WarungPkk::where('nama_warung_pkk', 'Warung PKK Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.warung-pkk.update', $warungPkk->id), [
            'nama_warung_pkk' => 'Warung PKK Anggrek',
            'nama_pengelola' => 'Nur Aini',
            'komoditi' => 'Telur ayam',
            'kategori' => 'Pangan',
            'volume' => '30 kg',
        ])->assertStatus(302);

        $this->assertDatabaseHas('warung_pkks', [
            'id' => $warungPkk->id,
            'komoditi' => 'Telur ayam',
            'volume' => '30 kg',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.warung-pkk.destroy', $warungPkk->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('warung_pkks', ['id' => $warungPkk->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_warung_pkk_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/warung-pkk');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_warung_pkk_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/warung-pkk');

        $response->assertStatus(403);
    }
}
