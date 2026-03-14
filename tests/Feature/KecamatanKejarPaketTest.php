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

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'kecamatan-pokja-ii']);
        Role::create(['name' => 'desa-pokja-ii']);

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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.kejar-paket.show', $kejarPaketLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_kejar_paket_tahun_anggaran_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

        $kejarPaket = KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Lama',
            'jenis_kejar_paket' => 'Paket C',
            'jumlah_warga_belajar_l' => 9,
            'jumlah_warga_belajar_p' => 11,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.kejar-paket.show', $kejarPaket->id))
            ->assertStatus(403);
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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $userStale->assignRole('kecamatan-pokja-ii');

        $response = $this->actingAs($userStale)->get('/kecamatan/kejar-paket');

        $response->assertStatus(403);
    }
}
