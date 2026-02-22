<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaAnggotaPokjaTest extends TestCase
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
    public function admin_desa_dapat_melihat_daftar_anggota_pokja_di_desanya_sendiri()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        AnggotaPokja::create([
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
            'pokja' => 'Pokja I',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        AnggotaPokja::create([
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
            'pokja' => 'Pokja II',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/anggota-pokja');

        $response->assertOk();
        $response->assertSee('Siti Aminah');
        $response->assertDontSee('Rina Lestari');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_data_anggota_pokja()
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/anggota-pokja', [
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
            'pokja' => 'Pokja III',
        ])->assertStatus(302);

        $anggotaPokja = AnggotaPokja::where('nama', 'Dewi Sartika')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.anggota-pokja.update', $anggotaPokja->id), [
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
            'pokja' => 'Pokja III',
        ])->assertStatus(302);

        $this->assertDatabaseHas('anggota_pokjas', [
            'id' => $anggotaPokja->id,
            'alamat' => 'Jl. Kenanga 33',
            'pendidikan' => 'S1',
            'keterangan' => 'Pengurus inti',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.anggota-pokja.destroy', $anggotaPokja->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('anggota_pokjas', ['id' => $anggotaPokja->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_anggota_pokja_desa()
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/anggota-pokja');

        $response->assertStatus(403);
    }
}
