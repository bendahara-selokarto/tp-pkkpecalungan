<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanDesaActivityTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

    protected Area $desaA1;

    protected Area $desaB1;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);

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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Kegiatan Desa Gombong',
            'description' => 'Dalam kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa Kalisalak',
            'description' => 'Luar kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $this->actingAs($kecamatanUser);

        $response = $this->get(route('kecamatan.desa-activities.index'));

        $response->assertOk();
        $response->assertSee('Kegiatan Desa Gombong');
        $response->assertDontSee('Kegiatan Desa Kalisalak');
    }

    #[Test]
    public function monitoring_kecamatan_tidak_membocorkan_kegiatan_desa_tahun_anggaran_lain(): void
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Monitoring Tahun Aktif',
            'description' => 'Dalam tahun aktif',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Activity::create([
            'title' => 'Monitoring Tahun Lama',
            'description' => 'Di luar tahun aktif',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => '2026-02-10',
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $response = $this->actingAs($kecamatanUser)->get(route('kecamatan.desa-activities.index'));

        $response->assertOk();
        $response->assertSee('Monitoring Tahun Aktif');
        $response->assertDontSee('Monitoring Tahun Lama');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page->where('filters.tahun_anggaran', self::ACTIVE_BUDGET_YEAR);
        });
    }

    #[Test]
    public function pengguna_kecamatan_tidak_dapat_membuka_detail_kegiatan_desa_di_luar_kecamatannya()
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

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
        $desaUser->assignRole('desa-sekretaris');

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
        $invalidKecamatanUser->assignRole('kecamatan-sekretaris');

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
        $kecamatanUser->assignRole('kecamatan-sekretaris');

        for ($index = 1; $index <= 11; $index++) {
            Activity::create([
                'title' => 'Kegiatan Desa Gombong '.$index,
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
                ->where('filters.per_page', 10)
                ->where('filters.desa_id', null)
                ->where('filters.status', null)
                ->where('filters.q', null);
        });
    }

    #[Test]
    public function daftar_kegiatan_desa_monitoring_bisa_difilter_berdasarkan_desa_status_dan_kata_kunci(): void
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

        $desaA2 = Area::create([
            'name' => 'Karanganyar',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        Activity::create([
            'title' => 'Monitoring Posyandu Gombong',
            'description' => 'Sesuai filter',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Monitoring Posyandu Karanganyar',
            'description' => 'Beda desa',
            'level' => 'desa',
            'area_id' => $desaA2->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Draft Posyandu Gombong',
            'description' => 'Beda status',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($kecamatanUser)->get(
            route('kecamatan.desa-activities.index', [
                'desa_id' => $this->desaA1->id,
                'status' => 'published',
                'q' => 'Posyandu Gombong',
            ])
        );

        $response->assertOk();
        $response->assertSee('Monitoring Posyandu Gombong');
        $response->assertDontSee('Monitoring Posyandu Karanganyar');
        $response->assertDontSee('Draft Posyandu Gombong');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DesaActivities/Index')
                ->where('filters.desa_id', $this->desaA1->id)
                ->where('filters.status', 'published')
                ->where('filters.q', 'Posyandu Gombong')
                ->has('desaOptions', 2)
                ->where('statusOptions.0.value', 'draft')
                ->where('statusOptions.1.value', 'published');
        });
    }

    #[Test]
    public function partial_reload_monitoring_kegiatan_desa_hanya_mengembalikan_prop_yang_diminta(): void
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Monitoring Posyandu Gombong',
            'description' => 'Sesuai filter',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $kecamatanUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($kecamatanUser)->get(
            route('kecamatan.desa-activities.index', [
                'desa_id' => $this->desaA1->id,
                'status' => 'published',
                'per_page' => 25,
            ])
        );

        $response->assertOk();
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DesaActivities/Index')
                ->reloadOnly(['activities', 'filters'], function (AssertableInertia $reload): void {
                    $reload
                        ->where('filters.desa_id', $this->desaA1->id)
                        ->where('filters.status', 'published')
                        ->where('filters.per_page', 25)
                        ->where('activities.per_page', 25)
                        ->has('activities.data', 1)
                        ->missing('desaOptions')
                        ->missing('statusOptions')
                        ->missing('pagination');
                });
        });
    }

    #[Test]
    public function filter_desa_di_luar_kecamatan_sendiri_tetap_tidak_membocorkan_data(): void
    {
        $kecamatanUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $kecamatanUser->assignRole('kecamatan-sekretaris');

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

        $response = $this->actingAs($kecamatanUser)->get(
            route('kecamatan.desa-activities.index', ['desa_id' => $this->desaB1->id])
        );

        $response->assertOk();
        $response->assertDontSee('Kegiatan Desa Gombong');
        $response->assertDontSee('Kegiatan Desa Kalisalak');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/DesaActivities/Index')
                ->where('activities.total', 0)
                ->has('activities.data', 0);
        });
    }

    #[Test]
    public function sekretaris_kecamatan_mode_desa_melihat_semua_desa_di_kecamatan_sendiri(): void
    {
        $sekretarisUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $sekretarisUser->assignRole('kecamatan-sekretaris');

        $desaA2 = Area::create([
            'name' => 'Karanganyar',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa Gombong',
            'description' => 'Dalam kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaA1->id,
            'created_by' => $sekretarisUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa Karanganyar',
            'description' => 'Dalam kecamatan user',
            'level' => 'desa',
            'area_id' => $desaA2->id,
            'created_by' => $sekretarisUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Kegiatan Desa Kalisalak',
            'description' => 'Luar kecamatan user',
            'level' => 'desa',
            'area_id' => $this->desaB1->id,
            'created_by' => $sekretarisUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->actingAs($sekretarisUser)->get(route('kecamatan.desa-activities.index'));

        $response->assertOk();
        $response->assertSee('Kegiatan Desa Gombong');
        $response->assertSee('Kegiatan Desa Karanganyar');
        $response->assertDontSee('Kegiatan Desa Kalisalak');
    }
}
