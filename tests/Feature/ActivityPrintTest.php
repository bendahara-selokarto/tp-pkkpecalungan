<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        $this->desaB = Area::create(['name' => 'Kalisalak', 'level' => 'desa', 'parent_id' => $this->kecamatanB->id]);
    }

    public function test_desa_user_can_print_own_activity_pdf(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $activity = Activity::create([
            'title' => 'Musyawarah',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_desa_user_cannot_print_other_desa_activity(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $activity = Activity::create([
            'title' => 'Kegiatan Desa Lain',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));

        $response->assertStatus(403);
    }

    public function test_kecamatan_user_can_print_own_kecamatan_and_child_desa_activity(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $kecamatanActivity = Activity::create([
            'title' => 'Rapat Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $desaActivity = Activity::create([
            'title' => 'Kegiatan Desa',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->get(route('kecamatan.activities.print', $kecamatanActivity->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('kecamatan.desa-activities.print', $desaActivity->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_print_still_follows_role_and_area_when_scope_column_is_not_synced(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $activity = Activity::create([
            'title' => 'Kegiatan Scope Tidak Sinkron',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $showResponse = $this->actingAs($user)->get(route('desa.activities.show', $activity->id));
        $showResponse->assertOk();
        $showResponse->assertSee(route('desa.activities.print', $activity->id), false);

        $printResponse = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));
        $printResponse->assertOk();
    }
}
