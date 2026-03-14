<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaAnggotaTimPenggerakTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);

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
    public function admin_desa_dapat_melihat_daftar_anggota_tim_penggerak_di_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        AnggotaTimPenggerak::create([
            'nama' => 'Siti Aminah',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 1',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        AnggotaTimPenggerak::create([
            'nama' => 'Rina Lestari',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-02-02',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 2',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/anggota-tim-penggerak');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/AnggotaTimPenggerak/Index')
                ->has('anggotaTimPenggeraks.data', 1)
                ->where('anggotaTimPenggeraks.data.0.nama', 'Siti Aminah')
                ->where('anggotaTimPenggeraks.total', 1)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function daftar_anggota_tim_penggerak_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        for ($index = 1; $index <= 12; $index++) {
            AnggotaTimPenggerak::create([
                'nama' => 'Anggota Desa ' . $index,
                'jabatan' => 'Anggota',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Batang',
                'tanggal_lahir' => '1990-01-01',
                'status_perkawinan' => 'kawin',
                'alamat' => 'Jl. Melati ' . $index,
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'keterangan' => null,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
                'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
            ]);
        }

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Bocor',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-02-02',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 99',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/anggota-tim-penggerak?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Anggota Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/AnggotaTimPenggerak/Index')
                ->has('anggotaTimPenggeraks.data', 2)
                ->where('anggotaTimPenggeraks.current_page', 2)
                ->where('anggotaTimPenggeraks.per_page', 10)
                ->where('anggotaTimPenggeraks.total', 12)
                ->where('filters.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function admin_desa_hanya_melihat_anggota_tim_penggerak_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Aktif',
            'jabatan' => 'Anggota',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Lama',
            'jabatan' => 'Anggota',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati Lama',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->actingAs($adminDesa)->get('/desa/anggota-tim-penggerak')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('anggotaTimPenggeraks.total', 1)
                ->where('anggotaTimPenggeraks.data.0.nama', 'Anggota Aktif')
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR));
    }

    #[Test]
    public function per_page_tidak_valid_di_anggota_tim_penggerak_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        AnggotaTimPenggerak::create([
            'nama' => 'Default Per Page',
            'jabatan' => 'Anggota',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/anggota-tim-penggerak?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/AnggotaTimPenggerak/Index')
                ->where('filters.per_page', 10)
                ->where('anggotaTimPenggeraks.per_page', 10)
                ->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_anggota_tim_penggerak()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('desa-sekretaris');

        $this->actingAs($adminDesa)->post('/desa/anggota-tim-penggerak', [
            'nama' => 'Dewi Sartika',
            'jabatan' => 'Bendahara',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 3',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Wiraswasta',
            'keterangan' => 'Aktif',
        ])->assertStatus(302);

        $anggota = AnggotaTimPenggerak::where('nama', 'Dewi Sartika')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.anggota-tim-penggerak.update', $anggota->id), [
            'nama' => 'Dewi Sartika',
            'jabatan' => 'Bendahara',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 33',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Wiraswasta',
            'keterangan' => 'Pengurus inti',
        ])->assertStatus(302);

        $this->assertDatabaseHas('anggota_tim_penggeraks', [
            'id' => $anggota->id,
            'alamat' => 'Jl. Kenanga 33',
            'pendidikan' => 'S1',
            'keterangan' => 'Pengurus inti',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.anggota-tim-penggerak.destroy', $anggota->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('anggota_tim_penggeraks', ['id' => $anggota->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_anggota_tim_penggerak_desa()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminKecamatan->assignRole('kecamatan-sekretaris');

        $response = $this->actingAs($adminKecamatan)->get('/desa/anggota-tim-penggerak');

        $response->assertStatus(403);
    }
}
