<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaTamanBacaanTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-ii']);
        Role::firstOrCreate(['name' => 'kecamatan-pokja-ii']);

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
    public function admin_desa_dapat_melihat_daftar_taman_bacaan_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'jumlah_buku_bacaan' => '200 buku',
            'jenis_buku' => 'Tanaman obat',
            'kategori' => 'Pertanian',
            'jumlah' => '40',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bacaan Melati',
            'nama_pengelola' => 'Rina Wati',
            'jumlah_buku_bacaan' => '120 buku',
            'jenis_buku' => 'Cerita anak',
            'kategori' => 'Pendidikan',
            'jumlah' => '25',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/taman-bacaan');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/TamanBacaan/Index')
                ->has('tamanBacaanItems.data', 1)
                ->where('tamanBacaanItems.data.0.nama_taman_bacaan', 'Taman Bacaan Mawar')
                ->where('tamanBacaanItems.total', 1)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function daftar_taman_bacaan_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        for ($index = 1; $index <= 12; $index++) {
            TamanBacaan::create([
                'nama_taman_bacaan' => 'Taman Bacaan Desa ' . $index,
                'nama_pengelola' => 'Pengelola ' . $index,
                'jumlah_buku_bacaan' => (100 + $index) . ' buku',
                'jenis_buku' => 'Jenis ' . $index,
                'kategori' => 'Kategori',
                'jumlah' => (string) $index,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
                'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            ]);
        }

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Bocor',
            'nama_pengelola' => 'Pengelola Bocor',
            'jumlah_buku_bacaan' => '1 buku',
            'jenis_buku' => 'Bocor',
            'kategori' => 'Bocor',
            'jumlah' => '1',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/taman-bacaan?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Taman Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/TamanBacaan/Index')
                ->has('tamanBacaanItems.data', 2)
                ->where('tamanBacaanItems.current_page', 2)
                ->where('tamanBacaanItems.per_page', 10)
                ->where('tamanBacaanItems.total', 12)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function admin_desa_hanya_melihat_taman_bacaan_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Aktif',
            'nama_pengelola' => 'Pengelola Default',
            'jumlah_buku_bacaan' => '100 buku',
            'jenis_buku' => 'Umum',
            'kategori' => 'Kategori',
            'jumlah' => '10',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Lama',
            'nama_pengelola' => 'Pengelola Lama',
            'jumlah_buku_bacaan' => '90 buku',
            'jenis_buku' => 'Lama',
            'kategori' => 'Kategori',
            'jumlah' => '8',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->actingAs($adminDesa)->get('/desa/taman-bacaan')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('tamanBacaanItems.total', 1)
                ->where('tamanBacaanItems.data.0.nama_taman_bacaan', 'Taman Aktif')
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR));
    }

    #[Test]
    public function per_page_tidak_valid_di_taman_bacaan_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        TamanBacaan::create([
            'nama_taman_bacaan' => 'Taman Default',
            'nama_pengelola' => 'Pengelola Default',
            'jumlah_buku_bacaan' => '100 buku',
            'jenis_buku' => 'Umum',
            'kategori' => 'Kategori',
            'jumlah' => '10',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/taman-bacaan?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/TamanBacaan/Index')
                ->where('filters.per_page', 10)
                ->where('tamanBacaanItems.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_taman_bacaan(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        $this->actingAs($adminDesa)->post('/desa/taman-bacaan', [
            'nama_taman_bacaan' => 'Taman Bacaan Anggrek',
            'nama_pengelola' => 'Nur Aini',
            'jumlah_buku_bacaan' => '150 buku',
            'jenis_buku' => 'Bacaan anak',
            'kategori' => 'Pendidikan',
            'jumlah' => '30',
        ])->assertStatus(302);

        $tamanBacaan = TamanBacaan::where('nama_taman_bacaan', 'Taman Bacaan Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.taman-bacaan.update', $tamanBacaan->id), [
            'nama_taman_bacaan' => 'Taman Bacaan Anggrek',
            'nama_pengelola' => 'Nur Aini',
            'jumlah_buku_bacaan' => '175 buku',
            'jenis_buku' => 'Keterampilan keluarga',
            'kategori' => 'Keterampilan',
            'jumlah' => '45',
        ])->assertStatus(302);

        $this->assertDatabaseHas('taman_bacaans', [
            'id' => $tamanBacaan->id,
            'jumlah_buku_bacaan' => '175 buku',
            'jenis_buku' => 'Keterampilan keluarga',
            'jumlah' => '45',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.taman-bacaan.destroy', $tamanBacaan->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('taman_bacaans', ['id' => $tamanBacaan->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_taman_bacaan_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

        $response = $this->actingAs($adminKecamatan)->get('/desa/taman-bacaan');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_taman_bacaan_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $userStale->assignRole('desa-pokja-ii');

        $response = $this->actingAs($userStale)->get('/desa/taman-bacaan');

        $response->assertStatus(403);
    }
}
