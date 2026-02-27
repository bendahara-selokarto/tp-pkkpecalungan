<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuDaftarHadirReportPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_buku_daftar_hadir_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $activity = Activity::create([
            'title' => 'Kegiatan Pokja Desa',
            'activity_date' => '2026-02-27',
            'description' => 'Kegiatan desa bulanan.',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-27',
            'activity_id' => $activity->id,
            'attendee_name' => 'Nur Kholis',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Hadir tepat waktu.',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.buku-daftar-hadir.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_buku_daftar_hadir_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $activity = Activity::create([
            'title' => 'Kegiatan Pokja Kecamatan',
            'activity_date' => '2026-02-27',
            'description' => 'Kegiatan kecamatan bulanan.',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        BukuDaftarHadir::create([
            'attendance_date' => '2026-02-27',
            'activity_id' => $activity->id,
            'attendee_name' => 'Slamet Riyadi',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Hadir sampai selesai.',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.buku-daftar-hadir.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_buku_daftar_hadir_tetap_aman_saat_role_dan_level_area_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.buku-daftar-hadir.report'));

        $response->assertStatus(403);
    }
}
