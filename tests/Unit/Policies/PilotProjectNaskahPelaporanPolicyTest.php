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

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_naskah_di_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-pokja-iv']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-pokja-iv');

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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(PilotProjectNaskahPelaporanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_naskah_kecamatan_lain(): void
    {
        Role::create(['name' => 'kecamatan-pokja-iv']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('kecamatan-pokja-iv');

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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(PilotProjectNaskahPelaporanPolicy::class);

        $this->assertFalse($policy->update($user, $laporanLuar));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_naskah_tahun_anggaran_lain_di_area_yang_sama(): void
    {
        Role::create(['name' => 'desa-pokja-iv']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-iv');

        $naskahTahunLama = PilotProjectNaskahPelaporanReport::create([
            'judul_laporan' => 'Naskah Tahun Lama',
            'dasar_pelaksanaan' => 'A',
            'pendahuluan' => 'A',
            'pelaksanaan_1' => '1',
            'pelaksanaan_2' => '2',
            'pelaksanaan_3' => '3',
            'pelaksanaan_4' => '4',
            'pelaksanaan_5' => '5',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(PilotProjectNaskahPelaporanPolicy::class);

        $this->assertFalse($policy->view($user, $naskahTahunLama));
    }
}
