<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaPrestasiLombaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_prestasi_lomba_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        PrestasiLomba::create([
            'tahun' => 2025,
            'jenis_lomba' => 'Lomba Administrasi PKK',
            'lokasi' => 'Aula Desa Gombong',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => 'Juara 1',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        PrestasiLomba::create([
            'tahun' => 2025,
            'jenis_lomba' => 'Lomba HATINYA PKK',
            'lokasi' => 'Aula Desa Bandung',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => true,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => 'Juara 2',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/prestasi-lomba');

        $response->assertOk();
        $response->assertSee('Lomba Administrasi PKK');
        $response->assertDontSee('Lomba HATINYA PKK');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_prestasi_lomba(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/prestasi-lomba', [
            'tahun' => 2025,
            'jenis_lomba' => 'Lomba Tertib Administrasi',
            'lokasi' => 'Balai Desa',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => 'Juara 1',
        ])->assertStatus(302);

        $prestasi = PrestasiLomba::where('jenis_lomba', 'Lomba Tertib Administrasi')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.prestasi-lomba.update', $prestasi->id), [
            'tahun' => 2025,
            'jenis_lomba' => 'Lomba Tertib Administrasi',
            'lokasi' => 'Balai Desa',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => true,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => 'Naik ke kabupaten',
        ])->assertStatus(302);

        $this->assertDatabaseHas('prestasi_lombas', [
            'id' => $prestasi->id,
            'prestasi_kabupaten' => true,
            'keterangan' => 'Naik ke kabupaten',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.prestasi-lomba.destroy', $prestasi->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('prestasi_lombas', ['id' => $prestasi->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_prestasi_lomba_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/prestasi-lomba');

        $response->assertStatus(403);
    }
}
