<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaBukuKeuanganTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_buku_keuangan_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        BukuKeuangan::create([
            'transaction_date' => '2026-02-01',
            'source' => 'kas_tunai',
            'description' => 'Iuran anggota',
            'reference_number' => 'BK-101',
            'entry_type' => 'pemasukan',
            'amount' => 1000000,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuKeuangan::create([
            'transaction_date' => '2026-02-05',
            'source' => 'bank',
            'description' => 'Biaya konsumsi rapat',
            'reference_number' => 'BK-102',
            'entry_type' => 'pengeluaran',
            'amount' => 500000,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/buku-keuangan');

        $response->assertOk();
        $response->assertSee('Iuran anggota');
        $response->assertDontSee('Biaya konsumsi rapat');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_buku_keuangan(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/buku-keuangan', [
            'transaction_date' => '2026-02-10',
            'source' => 'pusat',
            'description' => 'Transfer operasional',
            'reference_number' => 'BK-201',
            'entry_type' => 'pemasukan',
            'amount' => 2500000,
        ])->assertStatus(302);

        $entry = BukuKeuangan::where('description', 'Transfer operasional')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.buku-keuangan.update', $entry->id), [
            'transaction_date' => '2026-02-12',
            'source' => 'pusat',
            'description' => 'Transfer operasional revisi',
            'reference_number' => 'BK-201-R',
            'entry_type' => 'pemasukan',
            'amount' => 3000000,
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_keuangans', [
            'id' => $entry->id,
            'description' => 'Transfer operasional revisi',
            'reference_number' => 'BK-201-R',
            'amount' => 3000000,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-keuangan.destroy', $entry->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_keuangans', ['id' => $entry->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_buku_keuangan_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/buku-keuangan');

        $response->assertStatus(403);
    }
}
