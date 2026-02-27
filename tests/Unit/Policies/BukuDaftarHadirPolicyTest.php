<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\BukuDaftarHadir\Models\BukuDaftarHadir;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BukuDaftarHadirPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuDaftarHadirPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_daftar_hadir_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $activityA = Activity::create([
            'title' => 'Kegiatan A',
            'activity_date' => '2026-02-27',
            'description' => 'Kegiatan A',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $activityB = Activity::create([
            'title' => 'Kegiatan B',
            'activity_date' => '2026-02-27',
            'description' => 'Kegiatan B',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $milikSendiri = BukuDaftarHadir::create([
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityA->id,
            'attendee_name' => 'Peserta A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Hadir',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = BukuDaftarHadir::create([
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityB->id,
            'attendee_name' => 'Peserta B',
            'institution' => 'TP PKK Desa B',
            'description' => 'Hadir',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuDaftarHadirPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_daftar_hadir_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $activityB = Activity::create([
            'title' => 'Kegiatan Kecamatan B',
            'activity_date' => '2026-02-27',
            'description' => 'Kegiatan B',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $daftarHadirLuar = BukuDaftarHadir::create([
            'attendance_date' => '2026-02-27',
            'activity_id' => $activityB->id,
            'attendee_name' => 'Peserta Luar',
            'institution' => 'TP PKK Kecamatan B',
            'description' => 'Hadir',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuDaftarHadirPolicy::class);

        $this->assertFalse($policy->update($user, $daftarHadirLuar));
    }
}
