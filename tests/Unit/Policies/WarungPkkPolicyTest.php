<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\WarungPkkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarungPkkPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_warung_pkk_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Mawar',
            'nama_pengelola' => 'Siti Aminah',
            'komoditi' => 'Beras',
            'kategori' => 'Pangan',
            'volume' => '100 kg',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Melati',
            'nama_pengelola' => 'Rina Wati',
            'komoditi' => 'Sabun',
            'kategori' => 'Kebutuhan rumah tangga',
            'volume' => '90 pcs',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(WarungPkkPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_warung_pkk_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $warungPkkLuar = WarungPkk::create([
            'nama_warung_pkk' => 'Warung PKK Luar',
            'nama_pengelola' => 'Santi',
            'komoditi' => 'Minyak goreng',
            'kategori' => 'Pangan',
            'volume' => '40 liter',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(WarungPkkPolicy::class);

        $this->assertFalse($policy->update($user, $warungPkkLuar));
    }
}
