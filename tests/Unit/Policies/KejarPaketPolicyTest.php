<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\KejarPaketPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KejarPaketPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_kejar_paket_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Mawar',
            'jenis_kejar_paket' => 'Paket B',
            'jumlah_warga_belajar_l' => 18,
            'jumlah_warga_belajar_p' => 25,
            'jumlah_pengajar_l' => 2,
            'jumlah_pengajar_p' => 3,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Melati',
            'jenis_kejar_paket' => 'PAUD',
            'jumlah_warga_belajar_l' => 12,
            'jumlah_warga_belajar_p' => 14,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(KejarPaketPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_kejar_paket_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $kejarPaketLuar = KejarPaket::create([
            'nama_kejar_paket' => 'PKBM Luar',
            'jenis_kejar_paket' => 'Paket C',
            'jumlah_warga_belajar_l' => 9,
            'jumlah_warga_belajar_p' => 11,
            'jumlah_pengajar_l' => 1,
            'jumlah_pengajar_p' => 2,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(KejarPaketPolicy::class);

        $this->assertFalse($policy->update($user, $kejarPaketLuar));
    }
}
