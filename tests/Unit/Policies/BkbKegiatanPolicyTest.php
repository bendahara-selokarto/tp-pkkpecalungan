<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BkbKegiatanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BkbKegiatanPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_bkb_kegiatan_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        $milikSendiri = BkbKegiatan::create([
            'jumlah_kelompok' => 2,
            'jumlah_ibu_peserta' => 20,
            'jumlah_ape_set' => 3,
            'jumlah_kelompok_simulasi' => 1,
            'keterangan' => 'Wilayah sendiri',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = BkbKegiatan::create([
            'jumlah_kelompok' => 1,
            'jumlah_ibu_peserta' => 10,
            'jumlah_ape_set' => 2,
            'jumlah_kelompok_simulasi' => 1,
            'keterangan' => 'Wilayah lain',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(BkbKegiatanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_memperbarui_bkb_kegiatan_tahun_anggaran_lain(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('admin-desa');

        $bkbKegiatan = BkbKegiatan::create([
            'jumlah_kelompok' => 2,
            'jumlah_ibu_peserta' => 12,
            'jumlah_ape_set' => 2,
            'jumlah_kelompok_simulasi' => 1,
            'keterangan' => 'Tahun lama',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(BkbKegiatanPolicy::class);

        $this->assertFalse($policy->update($user, $bkbKegiatan));
    }
}
