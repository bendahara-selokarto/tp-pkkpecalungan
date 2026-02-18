<?php


use PHPUnit\\Framework\\Attributes\\Test;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBantuanTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_daftar_bantuan_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Bantuan::create([
            'name' => 'Bantuan Kabupaten Tahap 1',
            'category' => 'Keuangan',
            'description' => 'Untuk kecamatan A',
            'source' => 'kabupaten',
            'amount' => 45000000,
            'received_date' => '2026-02-07',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Bantuan::create([
            'name' => 'Bantuan Kecamatan B',
            'category' => 'Barang',
            'description' => 'Untuk kecamatan B',
            'source' => 'pihak_ketiga',
            'amount' => 15000000,
            'received_date' => '2026-02-08',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bantuans');

        $response->assertOk();
        $response->assertSee('Bantuan Kabupaten Tahap 1');
        $response->assertDontSee('Bantuan Kecamatan B');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_bantuan_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $bantuanLuar = Bantuan::create([
            'name' => 'Bantuan Luar Area',
            'category' => 'Keuangan',
            'description' => 'Luar wilayah',
            'source' => 'pusat',
            'amount' => 12000000,
            'received_date' => '2026-02-09',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.bantuans.show', $bantuanLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_bantuan_kecamatan()
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/bantuans');

        $response->assertStatus(403);
    }
}

