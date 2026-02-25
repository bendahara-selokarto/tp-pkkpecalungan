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

class KecamatanKaderKhususTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_daftar_kader_khusus_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        KaderKhusus::create([
            'nama' => 'Agus Setiawan',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1988-04-04',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Mawar 10',
            'pendidikan' => 'S1',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        KaderKhusus::create([
            'nama' => 'Budi Prasetyo',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1989-05-05',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Mawar 11',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Remaja',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/kader-khusus');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/KaderKhusus/Index')
                ->has('kaderKhususItems.data', 1)
                ->where('kaderKhususItems.data.0.nama', 'Agus Setiawan')
                ->where('kaderKhususItems.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_kader_khusus_kecamatan_mendukung_pagination_dan_tetap_scoped(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            KaderKhusus::create([
                'nama' => 'Kader Kec ' . $index,
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Batang',
                'tanggal_lahir' => '1990-01-01',
                'status_perkawinan' => 'kawin',
                'alamat' => 'Alamat ' . $index,
                'pendidikan' => 'SMA',
                'jenis_kader_khusus' => 'Kader Lansia',
                'keterangan' => null,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        KaderKhusus::create([
            'nama' => 'Kader Bocor',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Alamat Bocor',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Remaja',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/kader-khusus?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kader Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/KaderKhusus/Index')
                ->has('kaderKhususItems.data', 1)
                ->where('kaderKhususItems.current_page', 2)
                ->where('kaderKhususItems.per_page', 10)
                ->where('kaderKhususItems.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function per_page_tidak_valid_di_kader_khusus_kecamatan_kembali_ke_default(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        KaderKhusus::create([
            'nama' => 'Kader Default',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Alamat Default',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/kader-khusus?per_page=999');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/KaderKhusus/Index')
                ->where('filters.per_page', 10)
                ->where('kaderKhususItems.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_kader_khusus_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $kaderKhususLuar = KaderKhusus::create([
            'nama' => 'Tono Saputra',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1991-06-06',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Anggrek 9',
            'pendidikan' => 'D3',
            'jenis_kader_khusus' => 'Kader Disabilitas',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.kader-khusus.show', $kaderKhususLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_kader_khusus(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/kader-khusus', [
            'nama' => 'Lina Marlina',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-07-07',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Teratai 1',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Posyandu',
            'keterangan' => 'Data awal',
        ])->assertStatus(302);

        $kader = KaderKhusus::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('nama', 'Lina Marlina')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.kader-khusus.update', $kader->id), [
            'nama' => 'Lina Marlina',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-07-07',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Teratai 2',
            'pendidikan' => 'D3',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => 'Data revisi',
        ])->assertStatus(302);

        $this->assertDatabaseHas('kader_khusus', [
            'id' => $kader->id,
            'alamat' => 'Jl. Teratai 2',
            'pendidikan' => 'D3',
            'jenis_kader_khusus' => 'Kader Lansia',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_kader_khusus_kecamatan(): void
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/kader-khusus');

        $response->assertStatus(403);
    }
}
