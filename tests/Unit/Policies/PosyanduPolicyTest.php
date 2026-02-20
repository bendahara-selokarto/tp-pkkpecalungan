<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\PosyanduPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosyanduPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_posyandu_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = Posyandu::create([
            'nama_posyandu' => 'Posyandu Mawar',
            'nama_pengelola' => 'Siti',
            'nama_sekretaris' => 'Nina',
            'jenis_posyandu' => 'Pratama',
            'jumlah_kader' => 8,
            'jenis_kegiatan' => 'Penimbangan',
            'frekuensi_layanan' => 12,
            'jumlah_pengunjung_l' => 18,
            'jumlah_pengunjung_p' => 25,
            'jumlah_petugas_l' => 2,
            'jumlah_petugas_p' => 3,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'nama_pengelola' => 'Wati',
            'nama_sekretaris' => 'Rani',
            'jenis_posyandu' => 'Madya',
            'jumlah_kader' => 11,
            'jenis_kegiatan' => 'Imunisasi',
            'frekuensi_layanan' => 10,
            'jumlah_pengunjung_l' => 12,
            'jumlah_pengunjung_p' => 14,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PosyanduPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_posyandu_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $posyanduLuar = Posyandu::create([
            'nama_posyandu' => 'Posyandu Luar',
            'nama_pengelola' => 'Rika',
            'nama_sekretaris' => 'Maya',
            'jenis_posyandu' => 'Mandiri',
            'jumlah_kader' => 9,
            'jenis_kegiatan' => 'Konseling',
            'frekuensi_layanan' => 8,
            'jumlah_pengunjung_l' => 9,
            'jumlah_pengunjung_p' => 11,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PosyanduPolicy::class);

        $this->assertFalse($policy->update($user, $posyanduLuar));
    }
}
