<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanProgramPrioritasTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-kecamatan']);
        Role::create(['name' => 'admin-desa']);

        $this->kecamatanA = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->kecamatanB = Area::create([
            'name' => 'Limpung',
            'level' => 'kecamatan',
        ]);
    }

    #[Test]
    public function admin_kecamatan_dapat_melihat_program_prioritas_di_kecamatannya_sendiri(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        ProgramPrioritas::create([
            'program' => 'Program Kecamatan A',
            'prioritas_program' => 'Prioritas Tinggi',
            'kegiatan' => 'Pembinaan PKK A',
            'sasaran_target' => 'Seluruh desa A',
            'jadwal_i' => false,
            'jadwal_ii' => true,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => false,
            'sumber_dana_apbd' => true,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'A',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $adminKecamatan->id,
        ]);

        ProgramPrioritas::create([
            'program' => 'Program Kecamatan B',
            'prioritas_program' => 'Prioritas Sedang',
            'kegiatan' => 'Pembinaan PKK B',
            'sasaran_target' => 'Seluruh desa B',
            'jadwal_i' => false,
            'jadwal_ii' => true,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => false,
            'sumber_dana_apbd' => true,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'B',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)->get('/kecamatan/program-prioritas');

        $response->assertOk();
        $response->assertSee('Program Kecamatan A');
        $response->assertDontSee('Program Kecamatan B');
    }

    #[Test]
    public function admin_kecamatan_tidak_bisa_melihat_detail_program_prioritas_kecamatan_lain(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatanA->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $programLuar = ProgramPrioritas::create([
            'program' => 'Program Luar Area',
            'prioritas_program' => 'Prioritas',
            'kegiatan' => 'Kegiatan luar',
            'sasaran_target' => 'Sasaran luar',
            'jadwal_i' => true,
            'jadwal_ii' => false,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => false,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanB->id,
            'created_by' => $adminKecamatan->id,
        ]);

        $response = $this->actingAs($adminKecamatan)
            ->get(route('kecamatan.program-prioritas.show', $programLuar->id));

        $response->assertStatus(403);
    }

    #[Test]
    public function pengguna_non_admin_kecamatan_tidak_bisa_mengakses_modul_program_prioritas_kecamatan(): void
    {
        $desa = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatanA->id,
        ]);

        $adminDesa = User::factory()->create([
            'area_id' => $desa->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $response = $this->actingAs($adminDesa)->get('/kecamatan/program-prioritas');

        $response->assertStatus(403);
    }
}
