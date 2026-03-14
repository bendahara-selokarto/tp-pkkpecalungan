<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Policies\DataKegiatanWargaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DataKegiatanWargaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_data_kegiatan_warga_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'desa-pokja-i']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('desa-pokja-i');

        $milikSendiri = DataKegiatanWarga::create([
            'kegiatan' => 'Penghayatan dan Pengamalan Pancasila',
            'aktivitas' => true,
            'keterangan' => 'Pembinaan RT',
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Kerja bakti mingguan',
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataKegiatanWargaPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_data_kegiatan_warga_kecamatan_lain(): void
    {
        Role::create(['name' => 'kecamatan-pokja-i']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('kecamatan-pokja-i');

        $dataKegiatanWargaLuar = DataKegiatanWarga::create([
            'kegiatan' => 'Rukun Kematian',
            'aktivitas' => true,
            'keterangan' => 'Takziyah lintas RW',
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataKegiatanWargaPolicy::class);

        $this->assertFalse($policy->update($user, $dataKegiatanWargaLuar));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_data_kegiatan_warga_pada_tahun_anggaran_lain(): void
    {
        Role::create(['name' => 'desa-pokja-i']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-pokja-i');

        $dataKegiatanTahunLalu = DataKegiatanWarga::create([
            'kegiatan' => 'Kerja Bakti',
            'aktivitas' => true,
            'keterangan' => 'Tahun lalu',
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        $policy = app(DataKegiatanWargaPolicy::class);

        $this->assertFalse($policy->view($user, $dataKegiatanTahunLalu));
    }
}
