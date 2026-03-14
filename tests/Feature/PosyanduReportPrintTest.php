<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PosyanduReportPrintTest extends TestCase
{
    use RefreshDatabase;

    private const ACTIVE_BUDGET_YEAR = 2026;

    protected Area $kecamatanA;
    protected Area $kecamatanB;
    protected Area $desaA;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'desa-pokja-iv']);
        Role::firstOrCreate(['name' => 'kecamatan-sekretaris']);

        $this->kecamatanA = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $this->kecamatanB = Area::create(['name' => 'Limpung', 'level' => 'kecamatan']);
        $this->desaA = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $this->kecamatanA->id]);
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_posyandu_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-pokja-iv');

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Mawar',
            'nama_pengelola' => 'Siti',
            'nama_sekretaris' => 'Nina',
            'jenis_posyandu' => 'Pratama',
            'jumlah_kader' => 8,
            'jenis_kegiatan' => 'Penimbangan',
            'frekuensi_layanan' => 12,
            'jumlah_pengunjung_l' => 18,
            'jumlah_pengunjung_p' => 25,
            'jumlah_petugas_l' => 2,
            'jumlah_petugas_p' => 3,
            'keterangan' => 'Layanan balita rutin',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.posyandu.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_posyandu_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('kecamatan-sekretaris');

        Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'nama_pengelola' => 'Wati',
            'nama_sekretaris' => 'Rani',
            'jenis_posyandu' => 'Madya',
            'jumlah_kader' => 11,
            'jenis_kegiatan' => 'Imunisasi',
            'frekuensi_layanan' => 10,
            'jumlah_pengunjung_l' => 12,
            'jumlah_pengunjung_p' => 14,
            'jumlah_petugas_l' => 1,
            'jumlah_petugas_p' => 2,
            'keterangan' => 'Layanan imunisasi triwulan',
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.posyandu.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_posyandu_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id, 'active_budget_year' => self::ACTIVE_BUDGET_YEAR]);
        $user->assignRole('desa-pokja-iv');

        $response = $this->actingAs($user)->get(route('desa.posyandu.report'));

        $response->assertStatus(403);
    }

    public function test_header_kolom_pdf_posyandu_tetap_sesuai_mapping_autentik(): void
    {
        $html = view('pdf.posyandu_report', [
            'items' => collect(),
            'level' => 'desa',
            'areaName' => 'Gombong',
            'area' => null,
            'budgetYearLabel' => self::ACTIVE_BUDGET_YEAR,
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ])->render();

        $normalized = $this->normalizeText($html);
        $this->assertHeadersInOrder($normalized, [
            'NO',
            'JENIS KEGIATAN/LAYANAN',
            'FREKUENSI LAYANAN',
            'JUMLAH',
            'KETERANGAN',
            'PENGUNJUNG',
            'PETUGAS/PARAMEDIS',
            'L',
            'P',
            'L',
            'P',
        ]);
    }

    private function assertHeadersInOrder(string $normalizedHtml, array $headers): void
    {
        $cursor = 0;
        foreach ($headers as $header) {
            $needle = $this->normalizeText($header);
            $position = strpos($normalizedHtml, $needle, $cursor);

            $this->assertNotFalse($position, sprintf('Header "%s" tidak ditemukan/urutannya berubah.', $header));
            $cursor = $position + strlen($needle);
        }
    }

    private function normalizeText(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', strtoupper(strip_tags($text))));
    }
}
