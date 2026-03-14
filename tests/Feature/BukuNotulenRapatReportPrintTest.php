<?php

namespace Tests\Feature;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class BukuNotulenRapatReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-sekretaris']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_header_kolom_pdf_buku_notulen_rapat_tetap_stabil(): void
    {
        $this->assertPdfReportHeadersInOrder('pdf.buku_notulen_rapat_report', [
            'NO',
            'TANGGAL',
            'JUDUL RAPAT',
            'NAMA',
            'INSTANSI',
            'KETERANGAN',
        ]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_buku_notulen_rapat_desanya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Koordinasi Desa',
            'person_name' => 'Siti Aminah',
            'institution' => 'TP PKK Desa Gombong',
            'description' => 'Pembahasan rencana kerja bulanan.',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.buku-notulen-rapat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_buku_notulen_rapat_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        BukuNotulenRapat::create([
            'entry_date' => '2026-02-27',
            'title' => 'Rapat Koordinasi Kecamatan',
            'person_name' => 'Dewi Lestari',
            'institution' => 'TP PKK Kecamatan Pecalungan',
            'description' => 'Evaluasi pelaporan triwulan.',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.buku-notulen-rapat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_buku_notulen_rapat_tetap_aman_saat_role_dan_level_area_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $response = $this->actingAs($user)->get(route('desa.buku-notulen-rapat.report'));

        $response->assertStatus(403);
    }
}
