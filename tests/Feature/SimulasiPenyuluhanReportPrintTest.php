<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SimulasiPenyuluhanReportPrintTest extends TestCase
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

    public function test_admin_desa_dapat_mencetak_laporan_pdf_simulasi_penyuluhan_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Penyuluhan Gizi Balita',
            'jenis_simulasi_penyuluhan' => 'Penyuluhan',
            'jumlah_kelompok' => 3,
            'jumlah_sosialisasi' => 4,
            'jumlah_kader_l' => 1,
            'jumlah_kader_p' => 7,
            'keterangan' => null,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.simulasi-penyuluhan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_simulasi_penyuluhan_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        SimulasiPenyuluhan::create([
            'nama_kegiatan' => 'Simulasi Evakuasi Gempa',
            'jenis_simulasi_penyuluhan' => 'Simulasi',
            'jumlah_kelompok' => 6,
            'jumlah_sosialisasi' => 2,
            'jumlah_kader_l' => 5,
            'jumlah_kader_p' => 13,
            'keterangan' => null,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.simulasi-penyuluhan.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_simulasi_penyuluhan_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.simulasi-penyuluhan.report'));

        $response->assertStatus(403);
    }

    public function test_header_kolom_pdf_simulasi_penyuluhan_tetap_sesuai_mapping_autentik(): void
    {
        $html = view('pdf.simulasi_penyuluhan_report', [
            'items' => collect(),
            'level' => 'desa',
            'areaName' => 'Gombong',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ])->render();

        $normalized = $this->normalizeText($html);
        $this->assertHeadersInOrder($normalized, [
            'NO',
            'NAMA KEGIATAN',
            'JENIS SIMULASI/PENYULUHAN',
            'JUMLAH',
            'JUMLAH KADER',
            'KELOMPOK',
            'SOSIALISASI',
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

