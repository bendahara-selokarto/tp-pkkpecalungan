<?php

namespace Tests\Feature;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use App\Support\RoleScopeMatrix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KecamatanAgendaSuratTugasTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Area $area;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN, 'guard_name' => 'web']);
        
        $this->area = Area::create([
            'name' => 'Kecamatan Test',
            'level' => 'kecamatan'
        ]);

        $this->user = User::factory()->create([
            'area_id' => $this->area->id,
            'active_budget_year' => 2026,
        ]);
        
        $this->user->assignRole(RoleScopeMatrix::ROLE_SEKRETARIS_KECAMATAN);
    }

    public function test_kecamatan_sekretaris_can_access_agenda_surat_tugas(): void
    {
        $response = $this->actingAs($this->user)->get('/kecamatan/agenda-surat-tugas');

        $response->assertStatus(200);
    }

    public function test_kecamatan_sekretaris_can_create_agenda_surat_tugas(): void
    {
        $response = $this->actingAs($this->user)->post('/kecamatan/agenda-surat-tugas', [
            'nomor_surat' => '01/ST/2026',
            'tanggal_surat' => '2026-05-25',
            'kepada' => 'John Doe',
            'perihal' => 'Tugas Lapangan',
            'lampiran' => '1 berkas',
            'tembusan' => 'Ketua PKK',
        ]);

        $response->assertRedirect('/kecamatan/agenda-surat-tugas');
        $this->assertDatabaseHas('agenda_surat_tugas', [
            'nomor_surat' => '01/ST/2026',
            'kepada' => 'John Doe',
            'level' => 'kecamatan',
            'area_id' => $this->area->id,
        ]);
    }

    public function test_kecamatan_sekretaris_can_update_agenda_surat_tugas(): void
    {
        $item = AgendaSuratTugas::create([
            'nomor_surat' => '01/ST/2026',
            'tanggal_surat' => '2026-05-25',
            'kepada' => 'John Doe',
            'perihal' => 'Tugas Lapangan',
            'level' => 'kecamatan',
            'area_id' => $this->area->id,
            'tahun_anggaran' => 2026,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put("/kecamatan/agenda-surat-tugas/{$item->id}", [
            'nomor_surat' => '01/ST/2026-REV',
            'tanggal_surat' => '2026-05-25',
            'kepada' => 'Jane Doe',
            'perihal' => 'Tugas Lapangan Diperbarui',
            'lampiran' => '2 berkas',
            'tembusan' => 'Ketua PKK & Sekretaris',
        ]);

        $response->assertRedirect('/kecamatan/agenda-surat-tugas');
        $this->assertDatabaseHas('agenda_surat_tugas', [
            'id' => $item->id,
            'nomor_surat' => '01/ST/2026-REV',
            'kepada' => 'Jane Doe',
        ]);
    }

    public function test_kecamatan_sekretaris_can_print_pdf_agenda_surat_tugas(): void
    {
        $response = $this->actingAs($this->user)->get('/kecamatan/agenda-surat-tugas/report/pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
