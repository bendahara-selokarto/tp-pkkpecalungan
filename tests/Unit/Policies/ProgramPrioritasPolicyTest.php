<?php

namespace Tests\Unit\Policies;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Models\User;
use App\Policies\ProgramPrioritasPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProgramPrioritasPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_desa_hanya_boleh_melihat_program_prioritas_pada_desanya_sendiri(): void
    {
        Role::create(['name' => 'admin-desa']);

        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);
        $desaB = Area::create(['name' => 'Bandung', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $desaA->id]);
        $user->assignRole('admin-desa');

        $milikSendiri = ProgramPrioritas::create([
            'program' => 'Program A',
            'prioritas_program' => 'Utama',
            'kegiatan' => 'Kegiatan A',
            'sasaran_target' => 'Target A',
            'jadwal_i' => true,
            'jadwal_ii' => false,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => false,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaA->id,
            'created_by' => $user->id,
        ]);

        $milikDesaLain = ProgramPrioritas::create([
            'program' => 'Program B',
            'prioritas_program' => 'Menengah',
            'kegiatan' => 'Kegiatan B',
            'sasaran_target' => 'Target B',
            'jadwal_i' => true,
            'jadwal_ii' => false,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => false,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $desaB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(ProgramPrioritasPolicy::class);

        $this->assertTrue($policy->view($user, $milikSendiri));
        $this->assertFalse($policy->view($user, $milikDesaLain));
    }

    #[Test]
    public function admin_kecamatan_tidak_boleh_memperbarui_program_prioritas_kecamatan_lain(): void
    {
        Role::create(['name' => 'admin-kecamatan']);

        $kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);

        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $programLuar = ProgramPrioritas::create([
            'program' => 'Program Luar',
            'prioritas_program' => 'Tinggi',
            'kegiatan' => 'Kegiatan Luar',
            'sasaran_target' => 'Target Luar',
            'jadwal_i' => false,
            'jadwal_ii' => true,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => false,
            'sumber_dana_apbd' => true,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $kecamatanB->id,
            'created_by' => $user->id,
        ]);

        $policy = app(ProgramPrioritasPolicy::class);

        $this->assertFalse($policy->update($user, $programLuar));
    }
}
