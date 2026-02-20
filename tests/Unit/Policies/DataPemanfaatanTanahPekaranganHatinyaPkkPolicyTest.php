<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\DataPemanfaatanTanahPekaranganHatinyaPkkPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataPemanfaatanTanahPekaranganHatinyaPkkPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan' => 'Sejahtera I',
            'jumlah_kk_memanfaatkan' => 10,
            'keterangan' => 'Data sendiri',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan' => 'Sejahtera II',
            'jumlah_kk_memanfaatkan' => 12,
            'keterangan' => 'Data desa lain',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataPemanfaatanTanahPekaranganHatinyaPkkPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_data_pemanfaatan_tanah_pekarangan_hatinya_pkk_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $DataPemanfaatanTanahPekaranganHatinyaPkkLuar = DataPemanfaatanTanahPekaranganHatinyaPkk::create([
            'kategori_pemanfaatan' => 'Pra Sejahtera',
            'jumlah_kk_memanfaatkan' => 9,
            'keterangan' => 'Data luar',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataPemanfaatanTanahPekaranganHatinyaPkkPolicy::class);

        $this->assertFalse($policy->update($user, $DataPemanfaatanTanahPekaranganHatinyaPkkLuar));
    }
}


