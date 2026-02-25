<?php

namespace Tests\Feature;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaKaderKhususTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_kader_khusus_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        KaderKhusus::create([
            'nama' => 'Siti Aminah',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 1',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        KaderKhusus::create([
            'nama' => 'Rina Lestari',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-02-02',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 2',
            'pendidikan' => 'S1',
            'jenis_kader_khusus' => 'Kader Remaja',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/kader-khusus');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/KaderKhusus/Index')
                ->has('kaderKhususItems.data', 1)
                ->where('kaderKhususItems.data.0.nama', 'Siti Aminah')
                ->where('kaderKhususItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_kader_khusus_desa_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        for ($index = 1; $index <= 12; $index++) {
            KaderKhusus::create([
                'nama' => 'Kader Desa ' . $index,
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Batang',
                'tanggal_lahir' => '1990-01-01',
                'status_perkawinan' => 'kawin',
                'alamat' => 'Alamat ' . $index,
                'pendidikan' => 'SMA',
                'jenis_kader_khusus' => 'Kader Lansia',
                'keterangan' => null,
                'level' => 'desa',
                'area_id' => $this->desaA->id,
                'created_by' => $adminDesa->id,
            ]);
        }

        KaderKhusus::create([
            'nama' => 'Kader Bocor',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Alamat Bocor',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Remaja',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/kader-khusus?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kader Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/KaderKhusus/Index')
                ->has('kaderKhususItems.data', 2)
                ->where('kaderKhususItems.current_page', 2)
                ->where('kaderKhususItems.per_page', 10)
                ->where('kaderKhususItems.total', 12)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_kader_khusus_desa_kembali_ke_default(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        KaderKhusus::create([
            'nama' => 'Kader Default',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Alamat Default',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/kader-khusus?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Desa/KaderKhusus/Index')
                ->where('filters.per_page', 10)
                ->where('kaderKhususItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_kader_khusus(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/kader-khusus', [
            'nama' => 'Dewi Sartika',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 3',
            'pendidikan' => 'D3',
            'jenis_kader_khusus' => 'Kader Disabilitas',
            'keterangan' => 'Aktif',
        ])->assertStatus(302);

        $kaderKhusus = KaderKhusus::where('nama', 'Dewi Sartika')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.kader-khusus.update', $kaderKhusus->id), [
            'nama' => 'Dewi Sartika',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 33',
            'pendidikan' => 'S1',
            'jenis_kader_khusus' => 'Kader Disabilitas',
            'keterangan' => 'Pengurus inti',
        ])->assertStatus(302);

        $this->assertDatabaseHas('kader_khusus', [
            'id' => $kaderKhusus->id,
            'alamat' => 'Jl. Kenanga 33',
            'pendidikan' => 'S1',
            'keterangan' => 'Pengurus inti',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.kader-khusus.destroy', $kaderKhusus->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('kader_khusus', ['id' => $kaderKhusus->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_kader_khusus_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/kader-khusus');

        $response->assertStatus(403);
    }
}
