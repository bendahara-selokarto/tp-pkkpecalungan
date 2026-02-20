<?php

namespace Tests\Feature;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaKejarPaketTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_kejar_paket_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Mawar',
            'jenis_kejar_paket' => 'Paket B',
            'jumlah_warga_belajar_l' => 18,
            'jumlah_warga_belajar_p' => 25,
            'jumlah_pengajar_l' => 2,
            'jumlah_pengajar_p' => 3,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Melati',
            'jenis_kejar_paket' => 'PAUD',
            'jumlah_warga_belajar_l' => 10,
            'jumlah_warga_belajar_p' => 12,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/kejar-paket');

        $response->assertOk();
        $response->assertSee('PKBM Mawar');
        $response->assertDontSee('PKBM Melati');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_kejar_paket(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/kejar-paket', [
            'nama_kejar_paket' => 'PKBM Anggrek',
            'jenis_kejar_paket' => 'Paket A',
            'jumlah_warga_belajar_l' => 11,
            'jumlah_warga_belajar_p' => 13,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
        ])->assertStatus(302);

        $kejarPaket = KejarPaket::where('nama_kejar_paket', 'PKBM Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.kejar-paket.update', $kejarPaket->id), [
            'nama_kejar_paket' => 'PKBM Anggrek',
            'jenis_kejar_paket' => 'Paket C',
            'jumlah_warga_belajar_l' => 12,
            'jumlah_warga_belajar_p' => 15,
            'jumlah_pengajar_l' => 2,
            'jumlah_pengajar_p' => 3,
        ])->assertStatus(302);

        $this->assertDatabaseHas('kejar_pakets', [
            'id' => $kejarPaket->id,
            'jenis_kejar_paket' => 'Paket C',
            'jumlah_warga_belajar_l' => 12,
            'jumlah_warga_belajar_p' => 15,
            'jumlah_pengajar_p' => 3,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.kejar-paket.destroy', $kejarPaket->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('kejar_pakets', ['id' => $kejarPaket->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_kejar_paket_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/kejar-paket');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_kejar_paket_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/kejar-paket');

        $response->assertStatus(403);
    }
}
