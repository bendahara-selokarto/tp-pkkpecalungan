<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProgramPrioritasReportPrintTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_program_prioritas_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        ProgramPrioritas::create([
            'program' => 'Program Desa',
            'prioritas_program' => 'Utama',
            'kegiatan' => 'Pelatihan',
            'sasaran_target' => 'Kader',
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
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.program-prioritas.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_program_prioritas_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        ProgramPrioritas::create([
            'program' => 'Program Kecamatan',
            'prioritas_program' => 'Utama',
            'kegiatan' => 'Pembinaan',
            'sasaran_target' => 'Kader',
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
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.program-prioritas.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_tetap_aman_saat_role_dan_level_area_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.program-prioritas.report'));

        $response->assertStatus(403);
    }
}
