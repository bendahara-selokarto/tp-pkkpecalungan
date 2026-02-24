<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ZipArchive;

class LaporanTahunanPkkReportPrintTest extends TestCase
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

    public function test_admin_desa_dapat_mencetak_docx_laporan_tahunannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        Activity::create([
            'title' => 'Rapat Koordinasi Desa',
            'description' => 'Sinkronisasi program kerja tahunan',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'activity_date' => '2025-01-12',
            'status' => 'published',
        ]);

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahunan Desa',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.laporan-tahunan-pkk.print', $report->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('content-disposition');
        $this->assertValidDocxPayload(
            (string) $response->getContent(),
            ['Sinkronisasi program kerja tahunan']
        );
    }

    public function test_admin_kecamatan_dapat_mencetak_docx_laporan_tahunannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahunan Kecamatan',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.laporan-tahunan-pkk.print', $report->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('content-disposition');
        $this->assertValidDocxPayload((string) $response->getContent());
    }

    public function test_cetak_docx_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tidak Valid',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.laporan-tahunan-pkk.print', $report->id));

        $response->assertStatus(403);
    }

    /**
     * @param list<string> $expectedSnippets
     */
    private function assertValidDocxPayload(string $binary, array $expectedSnippets = []): void
    {
        $this->assertStringStartsWith('PK', $binary);

        $tmpFile = tempnam(sys_get_temp_dir(), 'laporan_tahunan_test_');
        if (! is_string($tmpFile) || $tmpFile === '') {
            throw new RuntimeException('Gagal membuat file sementara untuk validasi DOCX.');
        }

        file_put_contents($tmpFile, $binary);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($tmpFile) === true);
        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();
        @unlink($tmpFile);

        $this->assertIsString($documentXml);
        $this->assertStringContainsString('LAPORAN TAHUNAN', $documentXml);

        foreach ($expectedSnippets as $snippet) {
            $this->assertStringContainsString($snippet, $documentXml);
        }
    }
}
