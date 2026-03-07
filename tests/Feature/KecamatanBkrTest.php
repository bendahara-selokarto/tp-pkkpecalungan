<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBkrTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

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
    public function admin_kecamatan_dapat_melihat_bkr_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        Bkr::create([
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Anyelir',
            'no_tgl_sk' => '11/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Dewi Lestari',
            'jumlah_anggota' => 22,
            'kegiatan' => 'Kelas pendampingan keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Bkr::create([
            'desa' => 'Kragan',
            'nama_bkr' => 'BKR Dahlia',
            'no_tgl_sk' => '12/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Sri Handayani',
            'jumlah_anggota' => 19,
            'kegiatan' => 'Pelatihan komunikasi keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bkr');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bkr/Index')
                ->has('bkrItems.data', 1)
                ->where('bkrItems.data.0.nama_bkr', 'BKR Anyelir')
                ->where('bkrItems.total', 1)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function daftar_bkr_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Bkr::create([
                'desa' => 'Gombong',
                'nama_bkr' => 'BKR Kecamatan A ' . $index,
                'no_tgl_sk' => '11/SK/BKR/2026',
                'nama_ketua_kelompok' => 'Dewi Lestari',
                'jumlah_anggota' => 22,
                'kegiatan' => 'Kelas pendampingan keluarga',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
                'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            ]);
        }

        Bkr::create([
            'desa' => 'Kragan',
            'nama_bkr' => 'BKR Bocor',
            'no_tgl_sk' => '12/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Sri Handayani',
            'jumlah_anggota' => 19,
            'kegiatan' => 'Pelatihan komunikasi keluarga',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/bkr?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('BKR Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Bkr/Index')
                ->has('bkrItems.data', 1)
                ->where('bkrItems.current_page', 2)
                ->where('bkrItems.per_page', 10)
                ->where('bkrItems.total', 11)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_bkr_tahun_anggaran_lain_di_area_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $bkrTahunLain = Bkr::create([
            'desa' => 'Gombong',
            'nama_bkr' => 'BKR Arsip',
            'no_tgl_sk' => '98/SK/BKR/2025',
            'nama_ketua_kelompok' => 'Santi',
            'jumlah_anggota' => 10,
            'kegiatan' => 'Arsip',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.bkr.show', $bkrTahunLain->id))
            ->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_bkr_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $bkrLuar = Bkr::create([
            'desa' => 'Kragan',
            'nama_bkr' => 'BKR Luar Area',
            'no_tgl_sk' => '99/SK/BKR/2026',
            'nama_ketua_kelompok' => 'Santi',
            'jumlah_anggota' => 10,
            'kegiatan' => 'Kegiatan luar area',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.bkr.show', $bkrLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_bkr_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/bkr');

        $response->assertStatus(403);
    }
}
