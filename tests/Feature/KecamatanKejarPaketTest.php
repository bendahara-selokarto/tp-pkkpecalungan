<?php

namespace Tests\Feature;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanKejarPaketTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_kejar_paket_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Anyelir',
            'jenis_kejar_paket' => 'Paket B',
            'jumlah_warga_belajar_l' => 22,
            'jumlah_warga_belajar_p' => 27,
            'jumlah_pengajar_l' => 2,
            'jumlah_pengajar_p' => 4,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Dahlia',
            'jenis_kejar_paket' => 'PAUD',
            'jumlah_warga_belajar_l' => 14,
            'jumlah_warga_belajar_p' => 16,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 3,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/kejar-paket');

        $response->assertOk();
        $response->assertSee('PKBM Anyelir');
        $response->assertDontSee('PKBM Dahlia');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_kejar_paket_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $kejarPaketLuar = KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Luar Area',
            'jenis_kejar_paket' => 'Paket C',
            'jumlah_warga_belajar_l' => 9,
            'jumlah_warga_belajar_p' => 11,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.kejar-paket.show', $kejarPaketLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_kejar_paket_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/kejar-paket');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_kejar_paket_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $userStale = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $userStale->assignRole('admin-kecamatan');

        $response = $this->actingAs($userStale)->get('/kecamatan/kejar-paket');

        $response->assertStatus(403);
    }
}
