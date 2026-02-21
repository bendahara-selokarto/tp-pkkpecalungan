<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Models\User;
use App\Policies\PilotProjectNaskahPelaporanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PilotProjectNaskahPelaporanPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_naskah_di_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Desa A',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'A',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Desa B',
            'dasar_pelaksanaan' => 'B',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PilotProjectNaskahPelaporanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_naskah_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $laporanLuar = PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Luar',
            'dasar_pelaksanaan' => 'B',
            'pendahuluan' => 'B',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PilotProjectNaskahPelaporanPolicy::class);

        $this->assertFalse($policy->update($user, $laporanLuar));
    }
}
