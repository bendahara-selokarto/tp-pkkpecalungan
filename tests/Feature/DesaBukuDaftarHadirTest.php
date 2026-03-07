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

    private const ACTIVE_BUDGET_YEAR = 2026;

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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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
    public function admin_desa_hanya_melihat_daftar_hadir_pada_tahun_anggaran_aktif(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('admin-desa');

        $activityAktif = Activity::create([
            'title' => 'Kegiatan 2026',
            'activity_date' => '2026-02-26',
            'description' => 'Aktif',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $activityLama = Activity::create([
            'title' => 'Kegiatan 2025',
            'activity_date' => '2025-02-26',
            'description' => 'Lama',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-26',
            'activity_id' => $activityAktif->id,
            'attendee_name' => 'Peserta Tahun Aktif',
            'institution' => 'TP PKK Desa A',
            'description' => 'Masuk list',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2026,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2025-02-26',
            'activity_id' => $activityLama->id,
            'attendee_name' => 'Peserta Tahun Lama',
            'institution' => 'TP PKK Desa A',
            'description' => 'Tidak boleh muncul',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
            'tahun_anggaran' => 2025,
        ]);

        $this->actingAs($adminDesa)->get('/desa/buku-daftar-hadir')
            ->assertOk()
            ->assertInertia(function (AssertableInertia $page): void {
                $page
                    ->where('items.total', 1)
                    ->where('items.data.0.attendee_name', 'Peserta Tahun Aktif')
                    ->where('filters.tahun_anggaran', 2026);
            });
    }

    #[Test]
    public function admin_desa_tidak_bisa_membuat_daftar_hadir_dengan_kegiatan_tahun_anggaran_lain(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $adminDesa->assignRole('admin-desa');

        $activityLama = Activity::create([
            'title' => 'Kegiatan 2025',
            'activity_date' => '2025-02-26',
            'description' => 'Lama',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        $this->actingAs($adminDesa)
            ->post('/desa/buku-daftar-hadir', [
                'attendance_date' => '2026-02-27',
                'activity_id' => $activityLama->id,
                'attendee_name' => 'Peserta Tidak Valid',
                'institution' => 'TP PKK Desa Gombong',
                'description' => 'Harus ditolak.',
            ])
            ->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_daftar_hadir_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
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
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $staleUser->assignRole('admin-desa');

        $this->actingAs($staleUser)->get('/desa/buku-daftar-hadir')
            ->assertStatus(403);
    }
}
