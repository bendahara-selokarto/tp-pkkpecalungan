<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\DataIndustriRumahTanggaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataIndustriRumahTanggaPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_data_industri_rumah_tangga_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Pangan',
            'komoditi' => 'Ayam',
            'jumlah_komoditi' => '10 ekor',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Sandang',
            'komoditi' => 'Lele',
            'jumlah_komoditi' => '12 kolam',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataIndustriRumahTanggaPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_data_industri_rumah_tangga_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $DataIndustriRumahTanggaLuar = DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => 'Lain-lain',
            'komoditi' => 'Hidroponik',
            'jumlah_komoditi' => '9 instalasi',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataIndustriRumahTanggaPolicy::class);

        $this->assertFalse($policy->update($user, $DataIndustriRumahTanggaLuar));
    }
}




