<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Models\User;
use App\Policies\PraKoperasiUp2kPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PraKoperasiUp2kPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_pra_koperasi_up2k_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $milikSendiri = PraKoperasiUp2k::create([
            'tingkat' => 'pemula',
            'jumlah_kelompok' => 2,
            'jumlah_peserta' => 18,
            'keterangan' => 'Wilayah sendiri',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = PraKoperasiUp2k::create([
            'tingkat' => 'madya',
            'jumlah_kelompok' => 1,
            'jumlah_peserta' => 10,
            'keterangan' => 'Wilayah lain',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(PraKoperasiUp2kPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_memperbarui_pra_koperasi_up2k_tahun_anggaran_lain(): void
    {
        Role::create(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $praKoperasi = PraKoperasiUp2k::create([
            'tingkat' => 'utama',
            'jumlah_kelompok' => 1,
            'jumlah_peserta' => 9,
            'keterangan' => 'Tahun lama',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(PraKoperasiUp2kPolicy::class);

        $this->assertFalse($policy->update($user, $praKoperasi));
    }
}
