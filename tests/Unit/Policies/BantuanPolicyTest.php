<?php

namespace Tests\Unit\Policies;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\BantuanPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BantuanPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_bantuan_pada_desanya_sendiri()
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = Bantuan::create([
            'name' => 'Bantuan Pusat',
            'category' => 'Keuangan',
            'description' => null,
            'source' => 'pusat',
            'amount' => 2000000,
            'received_date' => '2026-02-11',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = Bantuan::create([
            'name' => 'Bantuan Provinsi',
            'category' => 'Barang',
            'description' => null,
            'source' => 'provinsi',
            'amount' => 3000000,
            'received_date' => '2026-02-12',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BantuanPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_bantuan_kecamatan_lain()
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $bantuanLuar = Bantuan::create([
            'name' => 'Bantuan Luar',
            'category' => 'Keuangan',
            'description' => null,
            'source' => 'kabupaten',
            'amount' => 5000000,
            'received_date' => '2026-02-13',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(BantuanPolicy::class);

        $this->assertFalse($policy->update($user, $bantuanLuar));
    }
}

