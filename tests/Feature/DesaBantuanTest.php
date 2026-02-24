<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBantuanTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_bantuan_di_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Bantuan::create([
            'name' => 'Dusun Krajan',
            'category' => 'uang',
            'description' => 'Dana dari pusat',
            'source' => 'pusat',
            'amount' => 10000000,
            'received_date' => '2026-02-01',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        Bantuan::create([
            'name' => 'Dusun Bandung',
            'category' => 'barang',
            'description' => 'Untuk desa lain',
            'source' => 'provinsi',
            'amount' => 5000000,
            'received_date' => '2026-02-05',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bantuans');

        $response->assertOk();
        $response->assertSee('Dusun Krajan');
        $response->assertDontSee('Dusun Bandung');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_bantuan()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/bantuans', [
            'lokasi_penerima' => 'Dusun Jatirejo',
            'jenis_bantuan' => 'uang',
            'keterangan' => 'Tahap awal',
            'asal_bantuan' => 'provinsi',
            'jumlah' => 25000000,
            'tanggal' => '2026-02-10',
        ])->assertStatus(302);

        $bantuan = Bantuan::where('name', 'Dusun Jatirejo')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.bantuans.update', $bantuan->id), [
            'lokasi_penerima' => 'Dusun Jatirejo',
            'jenis_bantuan' => 'uang',
            'keterangan' => 'Tahap final',
            'asal_bantuan' => 'provinsi',
            'jumlah' => 30000000,
            'tanggal' => '2026-02-12',
        ])->assertStatus(302);

        $this->assertDatabaseHas('bantuans', [
            'id' => $bantuan->id,
            'description' => 'Tahap final',
            'amount' => 30000000,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.bantuans.destroy', $bantuan->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('bantuans', ['id' => $bantuan->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_bantuan_desa()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/bantuans');

        $response->assertStatus(403);
    }
}
