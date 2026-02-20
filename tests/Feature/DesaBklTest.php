<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBklTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_bkl_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Bkl::create([
            'desa' => 'Gombong',
            'nama_bkl' => 'BKL Mawar',
            'no_tgl_sk' => '01/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Siti Aminah',
            'jumlah_anggota' => 25,
            'kegiatan' => 'Pertemuan bulanan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Bkl::create([
            'desa' => 'Bandung',
            'nama_bkl' => 'BKL Melati',
            'no_tgl_sk' => '02/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Rina Wati',
            'jumlah_anggota' => 30,
            'kegiatan' => 'Kelas pembinaan keluarga',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bkl');

        $response->assertOk();
        $response->assertSee('BKL Mawar');
        $response->assertDontSee('BKL Melati');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_bkl(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/bkl', [
            'desa' => 'Gombong',
            'nama_bkl' => 'BKL Anggrek',
            'no_tgl_sk' => '03/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Nur Aini',
            'jumlah_anggota' => 18,
            'kegiatan' => 'Penyuluhan gizi lansia',
        ])->assertStatus(302);

        $bkl = Bkl::where('nama_bkl', 'BKL Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.bkl.update', $bkl->id), [
            'desa' => 'Gombong',
            'nama_bkl' => 'BKL Anggrek',
            'no_tgl_sk' => '03/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Nur Aini',
            'jumlah_anggota' => 20,
            'kegiatan' => 'Penyuluhan gizi lansia dan senam',
        ])->assertStatus(302);

        $this->assertDatabaseHas('bkls', [
            'id' => $bkl->id,
            'jumlah_anggota' => 20,
            'kegiatan' => 'Penyuluhan gizi lansia dan senam',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.bkl.destroy', $bkl->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('bkls', ['id' => $bkl->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_bkl_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/bkl');

        $response->assertStatus(403);
    }
}
