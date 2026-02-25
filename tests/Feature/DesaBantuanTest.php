<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Bantuan/Index')
                ->has('bantuans.data', 1)
                ->where('bantuans.data.0.lokasi_penerima', 'Dusun Krajan')
                ->where('bantuans.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_bantuan_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            Bantuan::create([
                'name' => 'Dusun Gombong ' . $index,
                'category' => 'uang',
                'description' => 'Dana tahap ' . $index,
                'source' => 'pusat',
                'amount' => 1000000 + $index,
                'received_date' => now()->subDays($index)->toDateString(),
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        Bantuan::create([
            'name' => 'Dusun Bocor',
            'category' => 'barang',
            'description' => 'Untuk desa lain',
            'source' => 'provinsi',
            'amount' => 5000000,
            'received_date' => now()->toDateString(),
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bantuans?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Dusun Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Bantuan/Index')
                ->has('bantuans.data', 2)
                ->where('bantuans.current_page', 2)
                ->where('bantuans.per_page', 10)
                ->where('bantuans.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_bantuan_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        Bantuan::create([
            'name' => 'Dusun Default',
            'category' => 'uang',
            'description' => 'Default',
            'source' => 'pusat',
            'amount' => 1000000,
            'received_date' => '2026-02-01',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/bantuans?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/Bantuan/Index')
                ->where('filters.per_page', 10)
                ->where('bantuans.per_page', 10);
        });
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
