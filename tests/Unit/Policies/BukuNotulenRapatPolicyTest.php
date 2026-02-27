<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BukuNotulenRapatPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BukuNotulenRapatPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_notulen_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = BukuNotulenRapat::create([
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Desa A',
            'person_name' => 'Sekretaris A',
            'institution' => 'TP PKK Desa A',
            'description' => 'Notulen A',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = BukuNotulenRapat::create([
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Desa B',
            'person_name' => 'Sekretaris B',
            'institution' => 'TP PKK Desa B',
            'description' => 'Notulen B',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuNotulenRapatPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_notulen_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $notulenLuar = BukuNotulenRapat::create([
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Kecamatan Lain',
            'person_name' => 'Sekretaris Lain',
            'institution' => 'TP PKK Kecamatan Lain',
            'description' => 'Notulen luar wilayah',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BukuNotulenRapatPolicy::class);

        $this->assertFalse($policy->update($user, $notulenLuar));
    }
}
