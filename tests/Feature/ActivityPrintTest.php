<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityPrintTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'desa-sekretaris']);
        Role::create(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
        $this->desaB = Area::create(['name' => 'Kalisalak', 'level' => 'desa', 'parent_id' => $this->kecamatanB->id]);
    }

    public function test_pengguna_desa_dapat_mencetak_pdf_kegiatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-sekretaris');

        $activity = Activity::create([
            'title' => 'Musyawarah',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pengguna_desa_dapat_mencetak_pdf_daftar_kegiatan_all_pada_scopenya(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-sekretaris');

        Activity::create([
            'title' => 'Kegiatan A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        Activity::create([
            'title' => 'Kegiatan B',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.activities.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pengguna_desa_tidak_dapat_mencetak_pdf_kegiatan_tahun_anggaran_lain(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-sekretaris');

        $activity = Activity::create([
            'title' => 'Kegiatan Tahun Lama',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $response = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));

        $response->assertStatus(403);
    }

    public function test_pengguna_desa_tidak_dapat_mencetak_kegiatan_desa_lain(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-sekretaris');

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

    public function test_pengguna_kecamatan_dapat_mencetak_kegiatan_kecamatan_sendiri_dan_desa_turunan(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('kecamatan-sekretaris');

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

    public function test_pengguna_kecamatan_dapat_mencetak_pdf_daftar_kegiatan_all_pada_scopenya(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('kecamatan-sekretaris');

        Activity::create([
            'title' => 'Rapat Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        Activity::create([
            'title' => 'Evaluasi Kecamatan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.activities.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_cetak_tetap_mengikuti_peran_dan_area_saat_kolom_scope_belum_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->desaA->id]);
        $user->assignRole('desa-sekretaris');

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
        $showResponse->assertInertia(function (AssertableInertia $page) use ($activity) {
            $page
                ->component('Desa/Activities/Show')
                ->where('routes.print', route('desa.activities.print', $activity->id));
        });

        $printResponse = $this->actingAs($user)->get(route('desa.activities.print', $activity->id));
        $printResponse->assertOk();
    }
}
