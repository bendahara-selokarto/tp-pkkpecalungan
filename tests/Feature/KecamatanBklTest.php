<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBklTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_bkl_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Bkl::create([
            'desa' => 'Gombong',
            'nama_bkl' => 'BKL Anyelir',
            'no_tgl_sk' => '11/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Dewi Lestari',
            'jumlah_anggota' => 22,
            'kegiatan' => 'Kelas pendampingan keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        Bkl::create([
            'desa' => 'Kragan',
            'nama_bkl' => 'BKL Dahlia',
            'no_tgl_sk' => '12/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Sri Handayani',
            'jumlah_anggota' => 19,
            'kegiatan' => 'Pelatihan komunikasi keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bkl');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bkl/Index')
                ->has('bklItems.data', 1)
                ->where('bklItems.data.0.nama_bkl', 'BKL Anyelir')
                ->where('bklItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_bkl_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Bkl::create([
                'desa' => 'Gombong',
                'nama_bkl' => 'BKL Kecamatan A ' . $index,
                'no_tgl_sk' => '11/SK/BKL/2026',
                'nama_ketua_kelompok' => 'Dewi Lestari',
                'jumlah_anggota' => 22,
                'kegiatan' => 'Kelas pendampingan keluarga',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        Bkl::create([
            'desa' => 'Kragan',
            'nama_bkl' => 'BKL Bocor',
            'no_tgl_sk' => '12/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Sri Handayani',
            'jumlah_anggota' => 19,
            'kegiatan' => 'Pelatihan komunikasi keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bkl?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('BKL Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bkl/Index')
                ->has('bklItems.data', 1)
                ->where('bklItems.current_page', 2)
                ->where('bklItems.per_page', 10)
                ->where('bklItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_bkl_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $bklLuar = Bkl::create([
            'desa' => 'Kragan',
            'nama_bkl' => 'BKL Luar Area',
            'no_tgl_sk' => '99/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Santi',
            'jumlah_anggota' => 10,
            'kegiatan' => 'Kegiatan luar area',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.bkl.show', $bklLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_bkl_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/bkl');

        $response->assertStatus(403);
    }
}
