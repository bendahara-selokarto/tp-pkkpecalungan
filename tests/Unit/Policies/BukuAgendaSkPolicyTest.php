<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BukuAgendaSkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuAgendaSkPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_agenda_sk_pada_desanya_sendiri(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $milikSendiri = BukuAgendaSk::create([
            'nomor_sk' => 'SK/001',
            'tanggal_sk' => '2026-02-27',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat A',
            'tembusan' => 'Sekretariat',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = BukuAgendaSk::create([
            'nomor_sk' => 'SK/002',
            'tanggal_sk' => '2026-02-27',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat B',
            'tembusan' => 'Sekretariat',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(BukuAgendaSkPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_agenda_sk_tahun_anggaran_lain(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $agendaSk = BukuAgendaSk::create([
            'nomor_sk' => 'SK/003',
            'tanggal_sk' => '2025-02-27',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Lama',
            'tembusan' => 'Sekretariat',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => 2025,
        ]);

        $policy = app(BukuAgendaSkPolicy::class);

        $this->assertFalse($policy->view($user, $agendaSk));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_agenda_sk_kecamatan_lain(): void
    {
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $agendaSkLuar = BukuAgendaSk::create([
            'nomor_sk' => 'SK/004',
            'tanggal_sk' => '2026-02-27',
            'kepada' => 'Ketua TP PKK',
            'perihal' => 'Rapat Luar',
            'tembusan' => 'Sekretariat',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(BukuAgendaSkPolicy::class);

        $this->assertFalse($policy->update($user, $agendaSkLuar));
    }
}
