<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KoperasiReportPrintTest extends TestCase
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

    public function test_admin_desa_dapat_mencetak_laporan_pdf_koperasi_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Mawar',
            'jenis_usaha' => 'Simpan pinjam',
            'berbadan_hukum' => true,
            'belum_berbadan_hukum' => false,
            'jumlah_anggota_l' => 9,
            'jumlah_anggota_p' => 18,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.koperasi.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_koperasi_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        Koperasi::create([
            'nama_koperasi' => 'Koperasi Melati',
            'jenis_usaha' => 'Usaha bersama',
            'berbadan_hukum' => false,
            'belum_berbadan_hukum' => true,
            'jumlah_anggota_l' => 10,
            'jumlah_anggota_p' => 22,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.koperasi.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_koperasi_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.koperasi.report'));

        $response->assertStatus(403);
    }

    public function test_header_kolom_pdf_koperasi_tetap_sesuai_mapping_autentik(): void
    {
        $html = view('pdf.koperasi_report', [
            'items' => collect(),
            'level' => 'desa',
            'areaName' => 'Gombong',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ])->render();

        $normalized = $this->normalizeText($html);
        $this->assertHeadersInOrder($normalized, [
            'NO',
            'NAMA KOPERASI',
            'JENIS USAHA',
            'STATUS HUKUM',
            'JUMLAH ANGGOTA',
            'BERBADAN HUKUM',
            'BLM. BERBADAN HUKUM',
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
