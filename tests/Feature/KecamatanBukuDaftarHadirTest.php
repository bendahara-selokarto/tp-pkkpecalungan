<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanBukuDaftarHadirTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;

    protected Area $kecamatanB;

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
    }

    #[Test]
    public function admin_kecamatan_dapat_list_dan_crud_daftar_hadir_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $activityA = Activity::create([
            'title' => 'Kegiatan Kecamatan A',
            'activity_date' => '2026-02-26',
            'description' => 'Kegiatan kecamatan A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $activityB = Activity::create([
            'title' => 'Kegiatan Kecamatan B',
            'activity_date' => '2026-02-26',
            'description' => 'Kegiatan kecamatan B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-26',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Peserta Kecamatan A',
            'institution' => 'TP PKK Kecamatan A',
            'description' => 'Hadir',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-26',
            'activity_id' => $activityB->id,
            'attendee_name' => 'Peserta Kecamatan B',
            'institution' => 'TP PKK Kecamatan B',
            'description' => 'Hadir',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $this->actingAs($adminKecamatan)->get('/kecamatan/buku-daftar-hadir')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Kecamatan/BukuDaftarHadir/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.attendee_name', 'Peserta Kecamatan A')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminKecamatan)->post('/kecamatan/buku-daftar-hadir', [
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Kader Kecamatan',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Hadir lengkap.',
        ])->assertStatus(302);

        $created = BukuDaftarHadir::query()
            ->where('attendee_name', 'Kader Kecamatan')
            ->firstOrFail();

        $this->actingAs($adminKecamatan)->put(route('kecamatan.buku-daftar-hadir.update', $created->id), [
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Kader Kecamatan Updated',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Hadir sampai selesai.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_daftar_hadirs', [
            'id' => $created->id,
            'attendee_name' => 'Kader Kecamatan Updated',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
        ]);

        $this->actingAs($adminKecamatan)->delete(route('kecamatan.buku-daftar-hadir.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_daftar_hadirs', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_daftar_hadir_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->get('/kecamatan/buku-daftar-hadir')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_kecamatan_dengan_area_desa_ditolak(): void
    {
        $desa = Area::create([
            'name' => 'Sidorejo',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $staleUser = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'kecamatan',
        ]);
        $staleUser->assignRole('admin-kecamatan');

        $this->actingAs($staleUser)->get('/kecamatan/buku-daftar-hadir')
            ->assertStatus(403);
    }
}
