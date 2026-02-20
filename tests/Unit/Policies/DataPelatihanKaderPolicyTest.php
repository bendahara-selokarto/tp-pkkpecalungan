<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\DataPelatihanKaderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataPelatihanKaderPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_data_pelatihan_kader_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-1',
            'nama_lengkap_kader' => 'Kader A',
            'tanggal_masuk_tp_pkk' => '2020',
            'jabatan_fungsi' => 'Sekretaris',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Dasar',
            'jenis_kriteria_kaderisasi' => 'Dasar',
            'tahun_penyelenggaraan' => 2024,
            'institusi_penyelenggara' => 'TP PKK Kecamatan',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-2',
            'nama_lengkap_kader' => 'Kader B',
            'tanggal_masuk_tp_pkk' => '2019',
            'jabatan_fungsi' => 'Bendahara',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Lanjutan',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Kabupaten',
            'status_sertifikat' => 'Tidak',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataPelatihanKaderPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_data_pelatihan_kader_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $dataLuar = DataPelatihanKader::create([
            'nomor_registrasi' => 'REG-OUT',
            'nama_lengkap_kader' => 'Kader Luar',
            'tanggal_masuk_tp_pkk' => '2018',
            'jabatan_fungsi' => 'Pokja II',
            'nomor_urut_pelatihan' => 1,
            'judul_pelatihan' => 'Pelatihan Komunikasi',
            'jenis_kriteria_kaderisasi' => 'Lanjutan',
            'tahun_penyelenggaraan' => 2025,
            'institusi_penyelenggara' => 'TP PKK Provinsi',
            'status_sertifikat' => 'Bersertifikat',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataPelatihanKaderPolicy::class);

        $this->assertFalse($policy->update($user, $dataLuar));
    }
}
