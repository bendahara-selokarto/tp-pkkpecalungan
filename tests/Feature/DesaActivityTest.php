<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\Activities\Models\Activity;

class DesaActivityTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desa;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat role desa
        Role::create(['name' => 'desa']);

        // Buat Kecamatan
        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        // Buat Desa
        $this->desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    /** @test */
    public function desa_user_can_create_activity()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'desa',
        ]);

        $user->assignRole('desa');
        $this->actingAs($user);

        $response = $this->post('/desa/activities', [
            'title' => 'Musyawarah Desa',
            'description' => 'Rapat tahunan',
            'activity_date' => '2026-02-12',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('activities', [
            'title' => 'Musyawarah Desa',
            'area_id' => $this->desa->id,
        ]);
    }

    /** @test */
    public function desa_user_only_sees_their_own_activities()
    {
        $desaUser = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'desa',
        ]);
        $desaUser->assignRole('desa');

        // Desa lain
        $desaLain = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        // Activity desa user
        Activity::create([
            'title' => 'Desa Gombong Event',
            'level' => 'desa',
            'area_id' => $this->desa->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        // Activity desa lain
        Activity::create([
            'title' => 'Desa Bandung Event',
            'level' => 'desa',
            'area_id' => $desaLain->id,
            'created_by' => $desaUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($desaUser);

        $response = $this->get('/desa/activities');

        $response->assertSee('Desa Gombong Event');
        $response->assertDontSee('Desa Bandung Event');
    }

    /** @test */
    public function non_desa_user_cannot_access_desa_route()
    {
        $user = User::factory()->create([
            'area_id' => $this->desa->id,
            'scope'   => 'kecamatan',
        ]);

        $this->actingAs($user);

        $response = $this->get('/desa/activities');

        $response->assertStatus(403);
    }
}
