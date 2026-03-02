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

class DesaBukuDaftarHadirTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;

    protected Area $desaA;

    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_crud_dan_list_daftar_hadir_terbatas_pada_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $activityA = Activity::create([
            'title' => 'Kegiatan Desa A',
            'activity_date' => '2026-02-26',
            'description' => 'Kegiatan desa A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $activityB = Activity::create([
            'title' => 'Kegiatan Desa B',
            'activity_date' => '2026-02-26',
            'description' => 'Kegiatan desa B',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-26',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Peserta Desa A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Hadir tepat waktu',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-26',
            'activity_id' => $activityB->id,
            'attendee_name' => 'Peserta Desa B',
            'institution' => 'TP PKK Desa B',
            'description' => 'Hadir',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-daftar-hadir')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->component('Desa/BukuDaftarHadir/Index')
                    ->has('items.data', 1)
                    ->where('items.data.0.attendee_name', 'Peserta Desa A')
                    ->where('items.total', 1)
                    ->where('filters.per_page', 10);
            });

        $this->actingAs($adminDesa)->post('/desa/buku-daftar-hadir', [
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Kader Inti',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Hadir penuh sampai selesai.',
        ])->assertStatus(302);

        $created = BukuDaftarHadir::query()
            ->where('attendee_name', 'Kader Inti')
            ->firstOrFail();

        $this->assertDatabaseHas('buku_daftar_hadirs', [
            'id' => $created->id,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'activity_id' => $activityA->id,
        ]);

        $this->actingAs($adminDesa)->put(route('desa.buku-daftar-hadir.update', $created->id), [
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Kader Inti Updated',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Hadir sampai penutupan.',
        ])->assertStatus(302);

        $this->assertDatabaseHas('buku_daftar_hadirs', [
            'id' => $created->id,
            'attendee_name' => 'Kader Inti Updated',
            'description' => 'Hadir sampai penutupan.',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.buku-daftar-hadir.destroy', $created->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('buku_daftar_hadirs', ['id' => $created->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_daftar_hadir_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $this->actingAs($adminKecamatan)->get('/desa/buku-daftar-hadir')
            ->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_desa_dengan_area_kecamatan_ditolak(): void
    {
        $staleUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $staleUser->assignRole('admin-desa');

        $this->actingAs($staleUser)->get('/desa/buku-daftar-hadir')
            ->assertStatus(403);
    }
}
