<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaKoperasiTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_koperasi_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Mawar',
            'jenis_usaha' => 'Simpan pinjam',
            'berbadan_hukum' => true,
            'belum_berbadan_hukum' => false,
            'jumlah_anggota_l' => 12,
            'jumlah_anggota_p' => 25,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Melati',
            'jenis_usaha' => 'Usaha bersama',
            'berbadan_hukum' => false,
            'belum_berbadan_hukum' => true,
            'jumlah_anggota_l' => 9,
            'jumlah_anggota_p' => 18,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/koperasi');

        $response->assertOk();
        $response->assertSee('Koperasi Mawar');
        $response->assertDontSee('Koperasi Melati');
    }

    #[Test]
    public function admin_desa_hanya_melihat_koperasi_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Aktif',
            'jenis_usaha' => 'Simpan pinjam',
            'berbadan_hukum' => true,
            'belum_berbadan_hukum' => false,
            'jumlah_anggota_l' => 12,
            'jumlah_anggota_p' => 25,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Lama',
            'jenis_usaha' => 'Usaha bersama',
            'berbadan_hukum' => false,
            'belum_berbadan_hukum' => true,
            'jumlah_anggota_l' => 9,
            'jumlah_anggota_p' => 18,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->actingAs($adminDesa)->get('/desa/koperasi')
            ->assertOk()
            ->assertSee('Koperasi Aktif')
            ->assertDontSee('Koperasi Lama');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_koperasi(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-pokja-ii');

        $this->actingAs($adminDesa)->post('/desa/koperasi', [
            'nama_koperasi' => 'Koperasi Anggrek',
            'jenis_usaha' => 'Simpan pinjam',
            'berbadan_hukum' => true,
            'belum_berbadan_hukum' => false,
            'jumlah_anggota_l' => 8,
            'jumlah_anggota_p' => 16,
        ])->assertStatus(302);

        $koperasi = Koperasi::where('nama_koperasi', 'Koperasi Anggrek')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.koperasi.update', $koperasi->id), [
            'nama_koperasi' => 'Koperasi Anggrek',
            'jenis_usaha' => 'Usaha bersama',
            'berbadan_hukum' => false,
            'belum_berbadan_hukum' => true,
            'jumlah_anggota_l' => 10,
            'jumlah_anggota_p' => 20,
        ])->assertStatus(302);

        $this->assertDatabaseHas('koperasis', [
            'id' => $koperasi->id,
            'jenis_usaha' => 'Usaha bersama',
            'berbadan_hukum' => 0,
            'belum_berbadan_hukum' => 1,
            'jumlah_anggota_l' => 10,
            'jumlah_anggota_p' => 20,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.koperasi.destroy', $koperasi->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('koperasis', ['id' => $koperasi->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_koperasi_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-pokja-ii');

        $response = $this->actingAs($adminKecamatan)->get('/desa/koperasi');

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_role_desa_dengan_area_tidak_valid_tidak_bisa_mengakses_modul_koperasi_desa(): void
    {
        $userStale = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $userStale->assignRole('desa-pokja-ii');

        $response = $this->actingAs($userStale)->get('/desa/koperasi');

        $response->assertStatus(403);
    }
}
