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

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);
    }

    public function test_admin_desa_can_only_access_own_desa_activity(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

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
        $this->assertTrue($user->can('update', $ownActivity));
        $this->assertFalse($user->can('view', $otherActivity));
        $this->assertFalse($user->can('update', $otherActivity));
    }

    public function test_admin_kecamatan_can_view_child_desa_but_cannot_update_it(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatan->id]);
        $user->assignRole('admin-kecamatan');

        $desaActivity = Activity::create([
            'title' => 'Desa',
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $kecamatanActivity = Activity::create([
            'title' => 'Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $kecamatan->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->assertTrue($user->can('view', $desaActivity));
        $this->assertFalse($user->can('update', $desaActivity));
        $this->assertTrue($user->can('update', $kecamatanActivity));
    }
}
