<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use App\Models\User;
use App\Policies\TutorKhususPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TutorKhususPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_tutor_khusus_pada_desanya_sendiri(): void
    {
        Role::firstOrCreate(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $milikSendiri = TutorKhusus::create([
            'jenis_tutor' => 'kf',
            'jumlah_tutor' => 4,
            'keterangan' => 'Wilayah sendiri',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $milikDesaLain = TutorKhusus::create([
            'jenis_tutor' => 'paud',
            'jumlah_tutor' => 2,
            'keterangan' => 'Wilayah lain',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $policy = app(TutorKhususPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_memperbarui_tutor_khusus_tahun_anggaran_lain(): void
    {
        Role::firstOrCreate(['name' => 'desa-pokja-ii']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-ii');

        $tutorKhusus = TutorKhusus::create([
            'jenis_tutor' => 'kf',
            'jumlah_tutor' => 3,
            'keterangan' => 'Tahun lama',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(TutorKhususPolicy::class);

        $this->assertFalse($policy->update($user, $tutorKhusus));
    }
}
