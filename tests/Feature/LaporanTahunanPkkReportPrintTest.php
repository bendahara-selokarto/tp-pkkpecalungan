<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Activities\Models\Activity;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
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

    private const ACTIVE_BUDGET_YEAR = 2025;

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

    public function test_admin_desa_dapat_mencetak_docx_laporan_tahunannya_sendiri(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

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
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
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
        $user = User::factory()->create([
            'scope' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('kecamatan-sekretaris');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahunan Kecamatan',
            'tahun_laporan' => 2025,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.laporan-tahunan-pkk.print', $report->id));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->assertHeader('content-disposition');
        $this->assertValidDocxPayload((string) $response->getContent());
    }

    public function test_cetak_docx_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->kecamatanB->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tidak Valid',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.laporan-tahunan-pkk.print', $report->id));

        $response->assertStatus(403);
    }

    public function test_cetak_docx_hanya_memuat_agenda_surat_pada_tahun_anggaran_aktif(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2025-02-01',
            'tanggal_surat' => '2025-02-01',
            'nomor_surat' => '001/DSA/2025',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Kecamatan Pecalungan',
            'kepada' => 'TP PKK Desa Gombong',
            'perihal' => 'Undangan Musrenbang',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        AgendaSurat::create([
            'jenis_surat' => 'masuk',
            'tanggal_terima' => '2025-03-01',
            'tanggal_surat' => '2025-03-01',
            'nomor_surat' => '002/DSA/2025',
            'asal_surat' => 'Kecamatan',
            'dari' => 'Kecamatan Pecalungan',
            'kepada' => 'TP PKK Desa Gombong',
            'perihal' => 'Undangan Tahun Lama',
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahunan Desa',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR,
        ]);

        $response = $this->actingAs($user)->get(route('desa.laporan-tahunan-pkk.print', $report->id));

        $response->assertOk();
        $this->assertValidDocxPayload(
            (string) $response->getContent(),
            ['Agenda Surat MASUK: Undangan Musrenbang'],
            ['Agenda Surat MASUK: Undangan Tahun Lama']
        );
    }

    public function test_cetak_docx_menolak_laporan_tahun_anggaran_lain_meski_area_sama(): void
    {
        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $this->desaA->id,
            'active_budget_year' => self::ACTIVE_BUDGET_YEAR,
        ]);
        $user->assignRole('desa-sekretaris');

        $report = LaporanTahunanPkkReport::create([
            'judul_laporan' => 'Laporan Tahun Lama',
            'tahun_laporan' => 2025,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
            'tahun_anggaran' => self::ACTIVE_BUDGET_YEAR - 1,
        ]);

        $response = $this->actingAs($user)->get(route('desa.laporan-tahunan-pkk.print', $report->id));

        $response->assertStatus(403);
    }

    /**
     * @param  list<string>  $expectedSnippets
     * @param  list<string>  $unexpectedSnippets
     */
    private function assertValidDocxPayload(string $binary, array $expectedSnippets = [], array $unexpectedSnippets = []): void
    {
        $this->assertStringStartsWith('PK', $binary);

        $tmpFile = tempnam(sys_get_temp_dir(), 'laporan_tahunan_test_');
        if (! is_string($tmpFile) || $tmpFile === '') {
            throw new RuntimeException('Gagal membuat file sementara untuk validasi DOCX.');
        }

        file_put_contents($tmpFile, $binary);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($tmpFile) === true);
        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();
        @unlink($tmpFile);

        $this->assertIsString($documentXml);
        $this->assertStringContainsString('LAPORAN TAHUNAN', $documentXml);

        foreach ($expectedSnippets as $snippet) {
            $this->assertStringContainsString($snippet, $documentXml);
        }

        foreach ($unexpectedSnippets as $snippet) {
            $this->assertStringNotContainsString($snippet, $documentXml);
        }
    }
}
