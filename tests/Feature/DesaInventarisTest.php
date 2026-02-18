<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaInventarisTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_inventaris_di_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Inventaris::create([
            'name' => 'Kursi Balai Desa',
            'description' => 'Aset desa A',
            'quantity' => 10,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Inventaris::create([
            'name' => 'Meja Desa Lain',
            'description' => 'Aset desa B',
            'quantity' => 3,
            'unit' => 'buah',
            'condition' => 'rusak_ringan',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/inventaris');

        $response->assertOk();
        $response->assertSee('Kursi Balai Desa');
        $response->assertDontSee('Meja Desa Lain');
    }

    #[Test]
    public function admin_desa_dapat_melihat_detail_inventaris_di_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $inventaris = Inventaris::create([
            'name' => 'Lemari Arsip',
            'description' => 'Lemari utama',
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get(route('desa.inventaris.show', $inventaris->id));

        $response->assertOk();
        $response->assertSee('Lemari Arsip');
    }

    #[Test]
    public function admin_desa_dapat_menambah_data_inventaris_baru()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->post('/desa/inventaris', [
            'name' => 'Proyektor Aula',
            'description' => 'Untuk rapat warga',
            'quantity' => 1,
            'unit' => 'unit',
            'condition' => 'baik',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('inventaris', [
            'name' => 'Proyektor Aula',
            'area_id' => $this->desaA->id,
            'level' => 'desa',
        ]);
    }

    #[Test]
    public function admin_desa_dapat_memperbarui_data_inventaris_yang_dimiliki()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $inventaris = Inventaris::create([
            'name' => 'Sound System',
            'description' => 'Lama',
            'quantity' => 1,
            'unit' => 'set',
            'condition' => 'rusak_ringan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->put(route('desa.inventaris.update', $inventaris->id), [
            'name' => 'Sound System',
            'description' => 'Sudah diperbaiki',
            'quantity' => 2,
            'unit' => 'set',
            'condition' => 'baik',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('inventaris', [
            'id' => $inventaris->id,
            'description' => 'Sudah diperbaiki',
            'quantity' => 2,
            'condition' => 'baik',
        ]);
    }

    #[Test]
    public function admin_desa_dapat_menghapus_data_inventaris_yang_dimiliki()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $inventaris = Inventaris::create([
            'name' => 'Tenda',
            'description' => null,
            'quantity' => 4,
            'unit' => 'buah',
            'condition' => 'baik',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->delete(route('desa.inventaris.destroy', $inventaris->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('inventaris', ['id' => $inventaris->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_inventaris_desa()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/inventaris');

        $response->assertStatus(403);
    }
}


