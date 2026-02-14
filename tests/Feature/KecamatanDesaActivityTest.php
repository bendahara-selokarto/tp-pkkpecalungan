<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDesaActivityTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA1;
    protected Area $desaB1;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);

        $this->desaA1 = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $this->desaB1 = Area::create([
            'name' => 'Kalisalak',
            'level' => 'desa',
            'parent_id' => $this->kecamatanB->id,
        ]);
    }

    /** @test */
    public function kecamatan_user_can_list_desa_activities_in_their_kecamatan_only()
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('admin-kecamatan');

        Activity::create([
            'title' => 'Kegiatan Desa Gombong',
            'description' => 'Dalam kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa Kalisalak',
            'description' => 'Luar kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->actingAs($kecamatanUser);

        $response = $this->get(route('kecamatan.desa-activities.index'));

        $response->assertOk();
        $response->assertSee('Kegiatan Desa Gombong');
        $response->assertDontSee('Kegiatan Desa Kalisalak');
    }

    /** @test */
    public function kecamatan_user_cannot_open_detail_of_desa_activity_outside_their_kecamatan()
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('admin-kecamatan');

        $outsideActivity = Activity::create([
            'title' => 'Kegiatan Desa Kalisalak',
            'description' => 'Luar kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->actingAs($kecamatanUser);

        $response = $this->get(route('kecamatan.desa-activities.show', $outsideActivity->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function non_kecamatan_role_cannot_access_kecamatan_desa_activity_menu()
    {
        $desaUser = User::factory()->create([
            'area_id' => $this->desaA1->id,
            'scope' => 'desa',
        ]);
        $desaUser->assignRole('admin-desa');

        $this->actingAs($desaUser);

        $response = $this->get(route('kecamatan.desa-activities.index'));

        $response->assertStatus(403);
    }
}
