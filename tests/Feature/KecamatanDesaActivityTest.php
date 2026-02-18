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
    public function pengguna_kecamatan_dapat_melihat_daftar_kegiatan_desa_di_kecamatannya_saja()
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
    public function pengguna_kecamatan_tidak_dapat_membuka_detail_kegiatan_desa_di_luar_kecamatannya()
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
    public function peran_non_kecamatan_tidak_dapat_mengakses_menu_kegiatan_desa_kecamatan()
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

