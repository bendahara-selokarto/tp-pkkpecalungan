<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\Activities\UseCases\GetScopedActivityUseCase;
use App\Domains\Wilayah\Activities\UseCases\ListScopedActivitiesUseCase;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class KecamatanActivityTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'kecamatan-sekretaris']);
        Role::create(['name' => 'kecamatan-pokja-i']);
        Role::create(['name' => 'kecamatan-pokja-ii']);
        Role::create(['name' => 'kecamatan-pokja-iii']);
        Role::create(['name' => 'kecamatan-pokja-iv']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_membuat_dan_memperbarui_kegiatan(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->post('/kecamatan/activities', [
            'title' => 'Rapat Koordinasi Kecamatan',
            'nama_petugas' => 'Rini',
            'jabatan_petugas' => 'Sekretaris',
            'tempat_kegiatan' => 'Pendopo Kecamatan',
            'uraian' => 'Rapat bulanan',
            'tanda_tangan' => 'Rini',
            'activity_date' => '2026-02-22',
            'status' => 'draft',
        ])->assertStatus(302);

        $activity = Activity::query()
            ->where('area_id', $this->kecamatanA->id)
            ->where('title', 'Rapat Koordinasi Kecamatan')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.activities.update', $activity->id), [
            'title' => 'Rapat Koordinasi Kecamatan Revisi',
            'nama_petugas' => 'Rini',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Kecamatan',
            'uraian' => 'Rapat bulanan revisi',
            'tanda_tangan' => 'Rini',
            'activity_date' => '2026-02-23',
            'status' => 'published',
        ])->assertStatus(302);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Rapat Koordinasi Kecamatan Revisi',
            'jabatan_petugas' => 'Ketua',
            'tempat_kegiatan' => 'Aula Kecamatan',
            'uraian' => 'Rapat bulanan revisi',
            'description' => 'Rapat bulanan revisi',
            'activity_date' => '2026-02-23',
            'status' => 'published',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);
    }

    #[Test]
    public function tanggal_kegiatan_kecamatan_harus_format_yyyy_mm_dd(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->post('/kecamatan/activities', [
            'title' => 'Kegiatan Invalid',
            'description' => 'Uji format',
            'activity_date' => '23/02/2026',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('activity_date');
    }

    #[Test]
    public function daftar_kegiatan_kecamatan_menggunakan_payload_pagination(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        for ($index = 1; $index <= 11; $index++) {
            Activity::create([
                'title' => 'Kegiatan Kecamatan A ' . $index,
                'level' => 'kecamatan',
                'area_id' => $this->kecamatanA->id,
                'created_by' => $adminKecamatan->id,
                'activity_date' => now()->subDays($index)->toDateString(),
                'status' => 'draft',
            ]);
        }

        Activity::create([
            'title' => 'Kegiatan Kecamatan B Tidak Boleh Muncul',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/activities?page=2&per_page=10');

        $response->assertOk();
        $response->assertDontSee('Kegiatan Kecamatan B Tidak Boleh Muncul');
        $response->assertInertia(function (AssertableInertia $page): void {
            $page
                ->component('Kecamatan/Activities/Index')
                ->has('activities.data', 1)
                ->where('activities.current_page', 2)
                ->where('activities.per_page', 10)
                ->where('activities.total', 11)
                ->where('filters.per_page', 10);
        });
    }

    #[Test]
    public function pokja_i_kecamatan_hanya_melihat_kegiatan_pokja_i_pada_kecamatan_yang_sama_via_use_case(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Kecamatan Pokja I',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Kecamatan Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($pokjaIUser);
        $paginator = app(ListScopedActivitiesUseCase::class)->execute('kecamatan', 50);
        $titles = $paginator->getCollection()->pluck('title')->all();

        $this->assertContains('Kegiatan Kecamatan Pokja I', $titles);
        $this->assertNotContains('Kegiatan Kecamatan Pokja II', $titles);
    }

    #[Test]
    public function pokja_i_kecamatan_tidak_boleh_melihat_detail_kegiatan_pokja_lain_meski_satu_kecamatan(): void
    {
        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        $activityPokjaII = Activity::create([
            'title' => 'Detail Kecamatan Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $this->actingAs($pokjaIUser);

        try {
            app(GetScopedActivityUseCase::class)->execute($activityPokjaII->id, 'kecamatan');
            $this->fail('Expected HttpException 403 was not thrown.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    #[Test]
    public function sekretaris_kecamatan_tetap_melihat_semua_kegiatan_pada_area_kecamatan_yang_sama(): void
    {
        $sekretarisUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $sekretarisUser->assignRole('kecamatan-sekretaris');

        $pokjaIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIUser->assignRole('kecamatan-pokja-i');

        $pokjaIIUser = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $pokjaIIUser->assignRole('kecamatan-pokja-ii');

        Activity::create([
            'title' => 'Kegiatan Kec Pokja I',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        Activity::create([
            'title' => 'Kegiatan Kec Pokja II',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $pokjaIIUser->id,
            'activity_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        $response = $this->actingAs($sekretarisUser)->get('/kecamatan/activities');

        $response->assertOk();
        $response->assertSee('Kegiatan Kec Pokja I');
        $response->assertSee('Kegiatan Kec Pokja II');
    }
}
