<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaPosyanduTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_posyandu_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'nama_sekretaris' => 'Nina',
            'jenis_posyandu' => 'Pratama',
            'jumlah_kader' => 10,
            'jenis_kegiatan' => 'Penimbangan',
            'frekuensi_layanan' => 12,
            'jumlah_pengunjung_l' => 18,
            'jumlah_pengunjung_p' => 25,
            'jumlah_petugas_l' => 2,
            'jumlah_petugas_p' => 3,
            'keterangan' => 'Layanan rutin bulanan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'nama_pengelola' => 'Rina',
            'nama_sekretaris' => 'Maya',
            'jenis_posyandu' => 'Madya',
            'jumlah_kader' => 8,
            'jenis_kegiatan' => 'Imunisasi',
            'frekuensi_layanan' => 10,
            'jumlah_pengunjung_l' => 10,
            'jumlah_pengunjung_p' => 12,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'keterangan' => 'Data area lain',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/posyandu');

        $response->assertOk();
        $response->assertSee('Posyandu Mawar');
        $response->assertDontSee('Posyandu Melati');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_posyandu(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/posyandu', [
            'nama_posyandu' => 'Posyandu Anggrek',
            'nama_pengelola' => 'Dina',
            'nama_sekretaris' => 'Dewi',
            'jenis_posyandu' => 'Purnama',
            'jumlah_kader' => 9,
            'jenis_kegiatan' => 'Pemberian vitamin',
            'frekuensi_layanan' => 11,
            'jumlah_pengunjung_l' => 11,
            'jumlah_pengunjung_p' => 13,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'keterangan' => 'Entry awal',
        ])->assertStatus(302);

        $posyandu = Posyandu::where('nama_posyandu', 'Posyandu Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.posyandu.update', $posyandu->id), [
            'nama_posyandu' => 'Posyandu Anggrek',
            'nama_pengelola' => 'Dina',
            'nama_sekretaris' => 'Dewi',
            'jenis_posyandu' => 'Mandiri',
            'jumlah_kader' => 12,
            'jenis_kegiatan' => 'Pemeriksaan rutin',
            'frekuensi_layanan' => 12,
            'jumlah_pengunjung_l' => 12,
            'jumlah_pengunjung_p' => 15,
            'jumlah_petugas_l' => 2,
            'jumlah_petugas_p' => 3,
            'keterangan' => 'Entry revisi',
        ])->assertStatus(302);

        $this->assertDatabaseHas('posyandus', [
            'id' => $posyandu->id,
            'jenis_posyandu' => 'Mandiri',
            'jumlah_kader' => 12,
            'jumlah_pengunjung_l' => 12,
            'jumlah_pengunjung_p' => 15,
            'jumlah_petugas_p' => 3,
            'keterangan' => 'Entry revisi',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.posyandu.destroy', $posyandu->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('posyandus', ['id' => $posyandu->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_posyandu_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/posyandu');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_posyandu_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/posyandu');

        $response->assertStatus(403);
    }
}
