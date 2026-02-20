<?php

namespace Tests\Feature;

use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaDataKeluargaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_data_keluarga_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        DataKeluarga::create([
            'kategori_keluarga' => 'Sejahtera I',
            'jumlah_keluarga' => 20,
            'keterangan' => 'Data semester 1',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        DataKeluarga::create([
            'kategori_keluarga' => 'Sejahtera II',
            'jumlah_keluarga' => 15,
            'keterangan' => 'Data semester 1',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/data-keluarga');

        $response->assertOk();
        $response->assertSee('Sejahtera I');
        $response->assertDontSee('Sejahtera II');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_keluarga(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/data-keluarga', [
            'kategori_keluarga' => 'Pra Sejahtera',
            'jumlah_keluarga' => 12,
            'keterangan' => 'Pendataan awal',
        ])->assertStatus(302);

        $dataKeluarga = DataKeluarga::where('kategori_keluarga', 'Pra Sejahtera')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.data-keluarga.update', $dataKeluarga->id), [
            'kategori_keluarga' => 'Pra Sejahtera',
            'jumlah_keluarga' => 14,
            'keterangan' => 'Verifikasi ulang',
        ])->assertStatus(302);

        $this->assertDatabaseHas('data_keluargas', [
            'id' => $dataKeluarga->id,
            'jumlah_keluarga' => 14,
            'keterangan' => 'Verifikasi ulang',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.data-keluarga.destroy', $dataKeluarga->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('data_keluargas', ['id' => $dataKeluarga->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_data_keluarga_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/data-keluarga');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_data_keluarga_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $userStale->assignRole('admin-desa');

        $response = $this->actingAs($userStale)->get('/desa/data-keluarga');

        $response->assertStatus(403);
    }
}
