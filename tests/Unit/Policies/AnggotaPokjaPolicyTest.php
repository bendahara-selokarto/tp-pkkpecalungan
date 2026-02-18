<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\AnggotaPokjaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnggotaPokjaPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_anggota_pokja_pada_desanya_sendiri()
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = AnggotaPokja::create([
            'nama' => 'Nisa Khairunnisa',
            'jabatan' => 'Ketua',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Cendana 1',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
            'keterangan' => null,
            'pokja' => 'Pokja I',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = AnggotaPokja::create([
            'nama' => 'Maya Sari',
            'jabatan' => 'Sekretaris',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-02-02',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Cendana 2',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Wiraswasta',
            'keterangan' => null,
            'pokja' => 'Pokja II',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = new AnggotaPokjaPolicy();

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_anggota_pokja_kecamatan_lain()
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $anggotaPokjaLuar = AnggotaPokja::create([
            'nama' => 'Joko Widodo',
            'jabatan' => 'Bendahara',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1985-03-03',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Dahlia 3',
            'pendidikan' => 'D3',
            'pekerjaan' => 'Pegawai',
            'keterangan' => null,
            'pokja' => 'Pokja IV',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = new AnggotaPokjaPolicy();

        $this->assertFalse($policy->update($user, $anggotaPokjaLuar));
    }
}
