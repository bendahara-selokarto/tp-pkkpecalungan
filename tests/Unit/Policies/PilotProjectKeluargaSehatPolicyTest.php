<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Models\User;
use App\Policies\PilotProjectKeluargaSehatPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PilotProjectKeluargaSehatPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_laporan_di_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-pokja-iv']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-pokja-iv');

        $milikSendiri = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Desa A',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Desa B',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(PilotProjectKeluargaSehatPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_laporan_kecamatan_lain(): void
    {
        Role::create(['name' => 'kecamatan-pokja-iv']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('kecamatan-pokja-iv');

        $laporanLuar = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Luar',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(PilotProjectKeluargaSehatPolicy::class);

        $this->assertFalse($policy->update($user, $laporanLuar));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_laporan_pilot_project_tahun_anggaran_lain_di_area_yang_sama(): void
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

        $laporanTahunLama = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Tahun Lama',
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(PilotProjectKeluargaSehatPolicy::class);

        $this->assertFalse($policy->view($user, $laporanTahunLama));
    }
}
