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

    #[Test]
    public function admin_desa_hanya_boleh_melihat_laporan_di_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

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
        ]);

        $policy = app(PilotProjectKeluargaSehatPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_laporan_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

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
        ]);

        $policy = app(PilotProjectKeluargaSehatPolicy::class);

        $this->assertFalse($policy->update($user, $laporanLuar));
    }
}
