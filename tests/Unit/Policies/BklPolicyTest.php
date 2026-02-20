<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BklPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BklPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_bkl_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = Bkl::create([
            'desa' => 'Gombong',
            'nama_bkl' => 'BKL Mawar',
            'no_tgl_sk' => '01/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Siti Aminah',
            'jumlah_anggota' => 15,
            'kegiatan' => 'Pembinaan rutin',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = Bkl::create([
            'desa' => 'Bandung',
            'nama_bkl' => 'BKL Melati',
            'no_tgl_sk' => '02/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Rina Wati',
            'jumlah_anggota' => 18,
            'kegiatan' => 'Kelas keluarga',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BklPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_bkl_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $bklLuar = Bkl::create([
            'desa' => 'Kragan',
            'nama_bkl' => 'BKL Luar',
            'no_tgl_sk' => '99/SK/BKL/2026',
            'nama_ketua_kelompok' => 'Santi',
            'jumlah_anggota' => 12,
            'kegiatan' => 'Kegiatan luar area',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BklPolicy::class);

        $this->assertFalse($policy->update($user, $bklLuar));
    }
}
