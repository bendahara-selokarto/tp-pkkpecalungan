<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function pengguna_role_kecamatan_tetapi_area_desa_tidak_dapat_mengakses_menu_kecamatan(): void
    {
        $invalidKecamatanUser = User::factory()->create([
            'area_id' => $this->desaA1->id,
            'scope' => 'kecamatan',
        ]);
        $invalidKecamatanUser->assignRole('admin-kecamatan');

        $this->actingAs($invalidKecamatanUser);

        $response = $this->get(route('kecamatan.desa-activities.index'));

        $response->assertStatus(403);
    }

    #[Test]
    public function daftar_kegiatan_desa_di_kecamatan_memakai_pagination_payload(): void
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Activity::create([
                'title' => 'Kegiatan Desa Gombong ' . $index,
                'description' => 'Dalam kecamatan user',
                'level' => 'desa',
                'area_id' => $this->desaA1->id,
                'created_by' => $kecamatanUser->id,
                'activity_date' => now()->subDays($index)->toDateString(),
                'status' => 'published',
            ]);
        }

        Activity::create([
            'title' => 'Kegiatan Desa Kalisalak Tidak Boleh Muncul',
            'description' => 'Luar kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($kecamatanUser)->get('/kecamatan/desa-activities?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kegiatan Desa Kalisalak Tidak Boleh Muncul');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DesaActivities/Index')
                ->has('activities.data', 1)
                ->where('activities.current_page', 2)
                ->where('activities.per_page', 10)
                ->where('activities.total', 11)
                ->where('filters.per_page', 10);
        });
    }
}

