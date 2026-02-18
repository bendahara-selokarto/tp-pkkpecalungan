<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ScopedRoleActivityPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);
    }

    public function test_role_desa_sekretaris_hanya_bisa_data_desa_sendiri(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('desa-sekretaris');

        $ownActivity = Activity::create([
            'title' => 'Own',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $otherActivity = Activity::create([
            'title' => 'Other',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->assertTrue($user->can('view', $ownActivity));
        $this->assertFalse($user->can('view', $otherActivity));
    }

    public function test_role_kecamatan_sekretaris_bisa_lihat_desa_turunan(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatan->id]);
        $user->assignRole('kecamatan-sekretaris');

        $desaActivity = Activity::create([
            'title' => 'Desa',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->assertTrue($user->can('view', $desaActivity));
        $this->assertFalse($user->can('update', $desaActivity));
    }
}
