<?php

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\Test;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DesaProgramPrioritasTest extends TestCase
{
    use RefreshDatabase;

    protected Area $kecamatan;
    protected Area $desaA;
    protected Area $desaB;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin-desa']);
        Role::create(['name' => 'admin-kecamatan']);

        $this->kecamatan = Area::create([
            'name' => 'Pecalungan',
            'level' => 'kecamatan',
        ]);

        $this->desaA = Area::create([
            'name' => 'Gombong',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);

        $this->desaB = Area::create([
            'name' => 'Bandung',
            'level' => 'desa',
            'parent_id' => $this->kecamatan->id,
        ]);
    }

    #[Test]
    public function admin_desa_dapat_melihat_daftar_program_prioritas_di_desanya_sendiri(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        ProgramPrioritas::create([
            'program' => 'Program Desa A',
            'prioritas_program' => 'Prioritas Tinggi',
            'kegiatan' => 'Kegiatan Desa A',
            'sasaran_target' => 'Warga Desa A',
            'jadwal_i' => true,
            'jadwal_ii' => false,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => false,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'Catatan A',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $adminDesa->id,
        ]);

        ProgramPrioritas::create([
            'program' => 'Program Desa B',
            'prioritas_program' => 'Prioritas Sedang',
            'kegiatan' => 'Kegiatan Desa B',
            'sasaran_target' => 'Warga Desa B',
            'jadwal_i' => true,
            'jadwal_ii' => false,
            'jadwal_iii' => false,
            'jadwal_iv' => false,
            'sumber_dana_pusat' => false,
            'sumber_dana_apbd' => true,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'Catatan B',
            'level' => 'desa',
            'area_id' => $this->desaB->id,
            'created_by' => $adminDesa->id,
        ]);

        $response = $this->actingAs($adminDesa)->get('/desa/program-prioritas');

        $response->assertOk();
        $response->assertSee('Program Desa A');
        $response->assertDontSee('Program Desa B');
    }

    #[Test]
    public function admin_desa_dapat_menambah_memperbarui_dan_menghapus_program_prioritas(): void
    {
        $adminDesa = User::factory()->create([
            'area_id' => $this->desaA->id,
            'scope' => 'desa',
        ]);
        $adminDesa->assignRole('admin-desa');

        $this->actingAs($adminDesa)->post('/desa/program-prioritas', [
            'program' => 'Program Ketahanan Pangan',
            'prioritas_program' => 'Prioritas Utama',
            'kegiatan' => 'Pelatihan urban farming',
            'sasaran_target' => '25 kader',
            'jadwal_bulan_1' => true,
            'jadwal_bulan_2' => true,
            'jadwal_bulan_3' => false,
            'jadwal_bulan_4' => false,
            'jadwal_bulan_5' => false,
            'jadwal_bulan_6' => false,
            'jadwal_bulan_7' => false,
            'jadwal_bulan_8' => false,
            'jadwal_bulan_9' => false,
            'jadwal_bulan_10' => false,
            'jadwal_bulan_11' => false,
            'jadwal_bulan_12' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => false,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'Tahap awal',
        ])->assertStatus(302);

        $program = ProgramPrioritas::where('program', 'Program Ketahanan Pangan')->firstOrFail();

        $this->actingAs($adminDesa)->put(route('desa.program-prioritas.update', $program->id), [
            'program' => 'Program Ketahanan Pangan',
            'prioritas_program' => 'Prioritas Utama',
            'kegiatan' => 'Pelatihan urban farming',
            'sasaran_target' => '30 kader',
            'jadwal_bulan_1' => true,
            'jadwal_bulan_2' => true,
            'jadwal_bulan_3' => false,
            'jadwal_bulan_4' => true,
            'jadwal_bulan_5' => false,
            'jadwal_bulan_6' => false,
            'jadwal_bulan_7' => false,
            'jadwal_bulan_8' => false,
            'jadwal_bulan_9' => false,
            'jadwal_bulan_10' => false,
            'jadwal_bulan_11' => false,
            'jadwal_bulan_12' => false,
            'sumber_dana_pusat' => true,
            'sumber_dana_apbd' => true,
            'sumber_dana_swd' => false,
            'sumber_dana_bant' => false,
            'keterangan' => 'Tahap lanjutan',
        ])->assertStatus(302);

        $this->assertDatabaseHas('program_prioritas', [
            'id' => $program->id,
            'sasaran_target' => '30 kader',
            'jadwal_bulan_4' => true,
            'jadwal_ii' => true,
            'sumber_dana_apbd' => true,
            'keterangan' => 'Tahap lanjutan',
        ]);

        $this->actingAs($adminDesa)->delete(route('desa.program-prioritas.destroy', $program->id))
            ->assertStatus(302);

        $this->assertDatabaseMissing('program_prioritas', ['id' => $program->id]);
    }

    #[Test]
    public function pengguna_non_admin_desa_tidak_bisa_mengakses_modul_program_prioritas_desa(): void
    {
        $adminKecamatan = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'kecamatan',
        ]);
        $adminKecamatan->assignRole('admin-kecamatan');

        $response = $this->actingAs($adminKecamatan)->get('/desa/program-prioritas');

        $response->assertStatus(403);
    }

    #[Test]
    public function metadata_scope_stale_role_desa_dengan_area_kecamatan_ditolak(): void
    {
        $staleUser = User::factory()->create([
            'area_id' => $this->kecamatan->id,
            'scope' => 'desa',
        ]);
        $staleUser->assignRole('admin-desa');

        $response = $this->actingAs($staleUser)->get('/desa/program-prioritas');

        $response->assertStatus(403);
    }
}
