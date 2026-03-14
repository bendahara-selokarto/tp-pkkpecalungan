<?php

namespace Tests\Unit\Policies;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Models\User;
use App\Policies\PrestasiLombaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PrestasiLombaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_prestasi_lomba_pada_desanya_sendiri(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $milikSendiri = PrestasiLomba::create([
            'tahun' => self::ACTIVE_BUDGET_YEAR,
            'jenis_lomba' => 'Lomba A',
            'lokasi' => 'Gombong',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = PrestasiLomba::create([
            'tahun' => self::ACTIVE_BUDGET_YEAR,
            'jenis_lomba' => 'Lomba B',
            'lokasi' => 'Bandung',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => true,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PrestasiLombaPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_desa_tidak_boleh_melihat_prestasi_lomba_di_tahun_anggaran_lain(): void
    {
        Role::firstOrCreate(['name' => 'desa-sekretaris']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $prestasiTahunLama = PrestasiLomba::create([
            'tahun' => self::ACTIVE_BUDGET_YEAR - 1,
            'jenis_lomba' => 'Lomba Arsip',
            'lokasi' => 'Gombong',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $policy = app(PrestasiLombaPolicy::class);

        $this->assertFalse($policy->view($user, $prestasiTahunLama));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_prestasi_lomba_kecamatan_lain(): void
    {
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $prestasiLuar = PrestasiLomba::create([
            'tahun' => self::ACTIVE_BUDGET_YEAR,
            'jenis_lomba' => 'Lomba Luar',
            'lokasi' => 'Limpung',
            'prestasi_kecamatan' => true,
            'prestasi_kabupaten' => false,
            'prestasi_provinsi' => false,
            'prestasi_nasional' => false,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(PrestasiLombaPolicy::class);

        $this->assertFalse($policy->update($user, $prestasiLuar));
    }
}
