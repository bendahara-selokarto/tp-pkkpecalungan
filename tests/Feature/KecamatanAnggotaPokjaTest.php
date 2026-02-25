<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanAnggotaPokjaTest extends TestCase
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
    public function admin_kecamatan_dapat_melihat_daftar_anggota_pokja_di_kecamatannya_sendiri()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        AnggotaPokja::create([
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
            'pokja' => 'Pokja I',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        AnggotaPokja::create([
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
            'pokja' => 'Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/anggota-pokja');

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/AnggotaPokja/Index')
                ->has('anggotaPokjas.data', 1)
                ->where('anggotaPokjas.data.0.nama', 'Agus Setiawan')
                ->where('anggotaPokjas.total', 1)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function daftar_anggota_pokja_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            AnggotaPokja::create([
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
                'pokja' => 'Pokja I',
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
            ]);
        }

        AnggotaPokja::create([
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
            'pokja' => 'Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/anggota-pokja?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Anggota Kecamatan B Bocor');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/AnggotaPokja/Index')
                ->has('anggotaPokjas.data', 1)
                ->where('anggotaPokjas.current_page', 2)
                ->where('anggotaPokjas.per_page', 10)
                ->where('anggotaPokjas.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_anggota_pokja_kecamatan_lain()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $anggotaPokjaLuar = AnggotaPokja::create([
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
            'pokja' => 'Pokja IV',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get(route('kecamatan.anggota-pokja.show', $anggotaPokjaLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_kecamatan_dapat_menambah_dan_memperbarui_anggota_pokja()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/anggota-pokja', [
            'nama' => 'Rita Anggraini',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1991-06-06',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 10',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Wiraswasta',
            'pokja' => 'Pokja III',
            'keterangan' => 'Data awal',
        ])->assertStatus(302);

        $anggota = AnggotaPokja::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('nama', 'Rita Anggraini')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.anggota-pokja.update', $anggota->id), [
            'nama' => 'Rita Anggraini',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1991-06-06',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Kenanga 12',
            'pendidikan' => 'S2',
            'pekerjaan' => 'Wiraswasta',
            'pokja' => 'Pokja IV',
            'keterangan' => 'Data revisi',
        ])->assertStatus(302);

        $this->assertDatabaseHas('anggota_pokjas', [
            'id' => $anggota->id,
            'jabatan' => 'Ketua',
            'pokja' => 'Pokja IV',
            'alamat' => 'Jl. Kenanga 12',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_anggota_pokja_kecamatan()
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

        $response = $this->actingAs($adminDesa)->get('/kecamatan/anggota-pokja');

        $response->assertStatus(403);
    }
}
