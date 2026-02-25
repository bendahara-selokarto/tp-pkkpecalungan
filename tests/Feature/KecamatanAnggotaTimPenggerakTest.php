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

class KecamatanAnggotaTimPenggerakTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_daftar_anggota_tim_penggerak_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        AnggotaTimPenggerak::create([
            'nama' => 'Agus Setiawan',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1988-04-04',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Mawar 10',
            'pendidikan' => 'S1',
            'pekerjaan' => 'PNS',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        AnggotaTimPenggerak::create([
            'nama' => 'Budi Prasetyo',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1989-05-05',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Mawar 11',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Wiraswasta',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/anggota-tim-penggerak');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/AnggotaTimPenggerak/Index')
                ->has('anggotaTimPenggeraks.data', 1)
                ->where('anggotaTimPenggeraks.data.0.nama', 'Agus Setiawan')
                ->where('anggotaTimPenggeraks.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_anggota_tim_penggerak_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            AnggotaTimPenggerak::create([
                'nama' => 'Anggota Kecamatan A ' . $index,
                'jabatan' => 'Anggota',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Batang',
                'tanggal_lahir' => '1988-04-04',
                'status_perkawinan' => 'kawin',
                'alamat' => 'Jl. Mawar ' . $index,
                'pendidikan' => 'S1',
                'pekerjaan' => 'PNS',
                'keterangan' => null,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        AnggotaTimPenggerak::create([
            'nama' => 'Anggota Kecamatan B Bocor',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1989-05-05',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Mawar 99',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Wiraswasta',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/anggota-tim-penggerak?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Anggota Kecamatan B Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/AnggotaTimPenggerak/Index')
                ->has('anggotaTimPenggeraks.data', 1)
                ->where('anggotaTimPenggeraks.current_page', 2)
                ->where('anggotaTimPenggeraks.per_page', 10)
                ->where('anggotaTimPenggeraks.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_anggota_tim_penggerak_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $anggotaLuar = AnggotaTimPenggerak::create([
            'nama' => 'Tono Saputra',
            'jabatan' => 'Bendahara',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1991-06-06',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Anggrek 9',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Pegawai',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.anggota-tim-penggerak.show', $anggotaLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_anggota_tim_penggerak()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/anggota-tim-penggerak', [
            'nama' => 'Dewi Lestari',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 7',
            'pendidikan' => 'S1',
            'pekerjaan' => 'ASN',
            'keterangan' => 'Data awal',
        ])->assertStatus(302);

        $anggota = AnggotaTimPenggerak::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('nama', 'Dewi Lestari')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.anggota-tim-penggerak.update', $anggota->id), [
            'nama' => 'Dewi Lestari',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-04-23',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Melati 8',
            'pendidikan' => 'S2',
            'pekerjaan' => 'ASN',
            'keterangan' => 'Data revisi',
        ])->assertStatus(302);

        $this->assertDatabaseHas('anggota_tim_penggeraks', [
            'id' => $anggota->id,
            'jabatan' => 'Ketua',
            'alamat' => 'Jl. Melati 8',
            'pendidikan' => 'S2',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_anggota_tim_penggerak_kecamatan()
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/anggota-tim-penggerak');

        $response->assertStatus(403);
    }
}
