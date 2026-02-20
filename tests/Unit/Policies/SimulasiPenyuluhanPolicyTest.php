<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use App\Policies\SimulasiPenyuluhanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SimulasiPenyuluhanPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_simulasi_penyuluhan_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Penyuluhan A',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 2,
            'jumlah_sosialisasi' => 3,
            'jumlah_kader_l' => 1,
            'jumlah_kader_p' => 5,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi B',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 1,
            'jumlah_sosialisasi' => 1,
            'jumlah_kader_l' => 2,
            'jumlah_kader_p' => 2,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(SimulasiPenyuluhanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_simulasi_penyuluhan_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $simulasiLuar = SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi Luar',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 1,
            'jumlah_sosialisasi' => 1,
            'jumlah_kader_l' => 1,
            'jumlah_kader_p' => 1,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(SimulasiPenyuluhanPolicy::class);

        $this->assertFalse($policy->update($user, $simulasiLuar));
    }
}

