<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\KaderKhususPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KaderKhususPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_kader_khusus_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = KaderKhusus::create([
            'nama' => 'Nisa Khairunnisa',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1990-01-01',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Cendana 1',
            'pendidikan' => 'S1',
            'jenis_kader_khusus' => 'Kader Lansia',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = KaderKhusus::create([
            'nama' => 'Maya Sari',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1992-02-02',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Cendana 2',
            'pendidikan' => 'SMA',
            'jenis_kader_khusus' => 'Kader Remaja',
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(KaderKhususPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_kader_khusus_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $kaderKhususLuar = KaderKhusus::create([
            'nama' => 'Joko Widodo',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Batang',
            'tanggal_lahir' => '1985-03-03',
            'status_perkawinan' => 'kawin',
            'alamat' => 'Jl. Dahlia 3',
            'pendidikan' => 'D3',
            'jenis_kader_khusus' => 'Kader Disabilitas',
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(KaderKhususPolicy::class);

        $this->assertFalse($policy->update($user, $kaderKhususLuar));
    }
}
