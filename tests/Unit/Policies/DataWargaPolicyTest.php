<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\DataWargaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataWargaPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_data_warga_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = DataWarga::create([
            'dasawisma' => 'Mawar 01',
            'nama_kepala_keluarga' => 'Siti Aminah',
            'alamat' => 'RT 01 RW 02',
            'jumlah_warga_laki_laki' => 2,
            'jumlah_warga_perempuan' => 3,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = DataWarga::create([
            'dasawisma' => 'Melati 03',
            'nama_kepala_keluarga' => 'Rina Wati',
            'alamat' => 'RT 03 RW 01',
            'jumlah_warga_laki_laki' => 1,
            'jumlah_warga_perempuan' => 2,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataWargaPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_data_warga_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $dataWargaLuar = DataWarga::create([
            'dasawisma' => 'Luar 01',
            'nama_kepala_keluarga' => 'Santi',
            'alamat' => 'RW 10',
            'jumlah_warga_laki_laki' => 6,
            'jumlah_warga_perempuan' => 5,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataWargaPolicy::class);

        $this->assertFalse($policy->update($user, $dataWargaLuar));
    }
}
