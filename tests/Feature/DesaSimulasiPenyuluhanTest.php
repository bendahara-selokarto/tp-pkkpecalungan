<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaSimulasiPenyuluhanTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_simulasi_penyuluhan_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Penyuluhan Kesehatan Ibu',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 3,
            'jumlah_sosialisasi' => 5,
            'jumlah_kader_l' => 2,
            'jumlah_kader_p' => 8,
            'keterangan' => 'Rutin bulanan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi Tanggap Bencana',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 2,
            'jumlah_sosialisasi' => 1,
            'jumlah_kader_l' => 4,
            'jumlah_kader_p' => 6,
            'keterangan' => 'Lintas desa',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/simulasi-penyuluhan');

        $response->assertOk();
        $response->assertSee('Penyuluhan Kesehatan Ibu');
        $response->assertDontSee('Simulasi Tanggap Bencana');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_simulasi_penyuluhan(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/simulasi-penyuluhan', [
            'nama_kegiatan' => 'Penyuluhan Posyandu',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 4,
            'jumlah_sosialisasi' => 6,
            'jumlah_kader_l' => 1,
            'jumlah_kader_p' => 9,
            'keterangan' => 'Tahap awal',
        ])->assertStatus(302);

        $simulasi = SimulasiPenyuluhan::where('nama_kegiatan', 'Penyuluhan Posyandu')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.simulasi-penyuluhan.update', $simulasi->id), [
            'nama_kegiatan' => 'Penyuluhan Posyandu',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 4,
            'jumlah_sosialisasi' => 8,
            'jumlah_kader_l' => 2,
            'jumlah_kader_p' => 10,
            'keterangan' => 'Materi diperluas',
        ])->assertStatus(302);

        $this->assertDatabaseHas('simulasi_penyuluhans', [
            'id' => $simulasi->id,
            'jumlah_sosialisasi' => 8,
            'keterangan' => 'Materi diperluas',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.simulasi-penyuluhan.destroy', $simulasi->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('simulasi_penyuluhans', ['id' => $simulasi->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_simulasi_penyuluhan_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/simulasi-penyuluhan');

        $response->assertStatus(403);
    }
}

