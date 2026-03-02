<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BukuTamuPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuTamuPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_buku_tamu_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = BukuTamu::create([
            'visit_date' => '2026-02-27',
            'guest_name' => 'Siti Aminah',
            'purpose' => 'Konsultasi program',
            'institution' => 'TP PKK Desa A',
            'description' => 'Tamu A',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = BukuTamu::create([
            'visit_date' => '2026-02-27',
            'guest_name' => 'Budi Santoso',
            'purpose' => 'Koordinasi lintas desa',
            'institution' => 'TP PKK Desa B',
            'description' => 'Tamu B',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuTamuPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_buku_tamu_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $tamuLuar = BukuTamu::create([
            'visit_date' => '2026-02-27',
            'guest_name' => 'Slamet Riyadi',
            'purpose' => 'Verifikasi administrasi',
            'institution' => 'TP PKK Kecamatan Lain',
            'description' => 'Tamu luar wilayah',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuTamuPolicy::class);

        $this->assertFalse($policy->update($user, $tamuLuar));
    }
}
