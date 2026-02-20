<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanTamanBacaanTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_taman_bacaan_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Anyelir',
            'nama_pengelola' => 'Dewi Lestari',
            'jumlah_buku_bacaan' => '300 buku',
            'jenis_buku' => 'Tanaman hias',
            'kategori' => 'Pertanian',
            'jumlah' => '60',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Dahlia',
            'nama_pengelola' => 'Sri Handayani',
            'jumlah_buku_bacaan' => '180 buku',
            'jenis_buku' => 'Cerita anak',
            'kategori' => 'Pendidikan',
            'jumlah' => '35',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/taman-bacaan');

        $response->assertOk();
        $response->assertSee('Taman Bacaan Anyelir');
        $response->assertDontSee('Taman Bacaan Dahlia');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_taman_bacaan_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $tamanBacaanLuar = TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Luar Area',
            'nama_pengelola' => 'Santi',
            'jumlah_buku_bacaan' => '140 buku',
            'jenis_buku' => 'Keterampilan keluarga',
            'kategori' => 'Keterampilan',
            'jumlah' => '20',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.taman-bacaan.show', $tamanBacaanLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_taman_bacaan_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/taman-bacaan');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_kecamatan_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_taman_bacaan_kecamatan(): void
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

        $response = $this->actingAs($userStale)->get('/kecamatan/taman-bacaan');

        $response->assertStatus(403);
    }
}
