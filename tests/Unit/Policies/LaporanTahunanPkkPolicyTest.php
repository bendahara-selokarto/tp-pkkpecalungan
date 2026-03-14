<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\LaporanTahunanPkkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LaporanTahunanPkkPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2025;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_laporan_di_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $milikSendiri = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Desa A',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Desa B',
            'tahun_laporan' => 2024,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(LaporanTahunanPkkPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_laporan_kecamatan_lain(): void
    {
        Role::create(['name' => 'kecamatan-sekretaris']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $laporanLuar = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Kecamatan B',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(LaporanTahunanPkkPolicy::class);

        $this->assertFalse($policy->update($user, $laporanLuar));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_laporan_tahun_anggaran_lain_meski_area_sama(): void
    {
        Role::create(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $laporanTahunLama = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahun Lama',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(LaporanTahunanPkkPolicy::class);

        $this->assertFalse($policy->view($user, $laporanTahunLama));
    }
}
