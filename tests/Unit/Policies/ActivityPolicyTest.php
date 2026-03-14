<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);
    }

    public function test_admin_desa_hanya_dapat_mengakses_kegiatan_desanya_sendiri(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-sekretaris');

        $ownActivity = Activity::create([
            'title' => 'Own',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $otherActivity = Activity::create([
            'title' => 'Other',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->assertTrue($user->can('view', $ownActivity));
        $this->assertTrue($user->can('update', $ownActivity));
        $this->assertFalse($user->can('view', $otherActivity));
        $this->assertFalse($user->can('update', $otherActivity));
    }

    public function test_admin_kecamatan_dapat_melihat_kegiatan_desa_turunan_tetapi_tidak_dapat_memperbaruinya(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatan->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('kecamatan-sekretaris');

        $desaActivity = Activity::create([
            'title' => 'Desa',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $kecamatanActivity = Activity::create([
            'title' => 'Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->assertTrue($user->can('view', $desaActivity));
        $this->assertFalse($user->can('update', $desaActivity));
        $this->assertTrue($user->can('update', $kecamatanActivity));
    }

    public function test_admin_desa_tidak_dapat_melihat_kegiatan_tahun_anggaran_lain_meski_area_sama(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desa->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-sekretaris');

        $oldBudgetYearActivity = Activity::create([
            'title' => 'Old',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $this->assertFalse($user->can('view', $oldBudgetYearActivity));
        $this->assertFalse($user->can('update', $oldBudgetYearActivity));
    }
}
