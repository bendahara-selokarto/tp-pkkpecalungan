<?php

namespace Tests\Feature;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\Feature\Concerns\AssertsPdfReportHeaders;
use Tests\TestCase;

class PilotProjectKeluargaSehatReportPrintTest extends TestCase
{
    use RefreshDatabase;
    use AssertsPdfReportHeaders;

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

    public function test_header_kolom_pdf_pilot_project_tetap_sesuai_kontrak(): void
    {
        $html = view('pdf.pilot_project_keluarga_sehat_report', [
            'reports' => collect([
                (object) [
                    'judul_laporan' => 'Contoh',
                    'tahun_awal' => 2021,
                    'tahun_akhir' => 2021,
                    'values' => collect(),
                ],
            ]),
            'sections' => config('pilot_project_keluarga_sehat.sections', []),
            'level' => 'desa',
            'areaName' => 'Contoh Area',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ])->render();

        $normalizedContent = $this->normalizeText($html);
        foreach ([
            'NO',
            'DATA UTAMA YANG DI MONITOR',
            '2021',
            'I',
            'II',
            'EVALUASI',
            'KETERANGAN',
        ] as $header) {
            $needle = $this->normalizeText($header);
            $this->assertStringContainsString($needle, $normalizedContent);
        }
    }

    public function test_admin_desa_dapat_mencetak_laporan_pdf_pilot_project_desanya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->desaA->id]);
        $user->assignRole('admin-desa');

        $report = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Desa',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        PilotProjectKeluargaSehatValue::create([
            'report_id' => $report->id,
            'section' => 'data_dukung',
            'cluster_code' => 'SUPPORT',
            'indicator_code' => 'jumlah_penduduk',
            'indicator_label' => 'Jumlah penduduk',
            'year' => 2021,
            'semester' => 1,
            'value' => 120,
            'evaluation_note' => null,
            'sort_order' => 1,
            'level' => 'desa',
            'area_id' => $this->desaA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('desa.pilot-project-keluarga-sehat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_admin_kecamatan_dapat_mencetak_laporan_pdf_pilot_project_kecamatannya_sendiri(): void
    {
        $user = User::factory()->create(['scope' => 'kecamatan', 'area_id' => $this->kecamatanA->id]);
        $user->assignRole('admin-kecamatan');

        $report = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Kecamatan',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        PilotProjectKeluargaSehatValue::create([
            'report_id' => $report->id,
            'section' => 'data_dukung',
            'cluster_code' => 'SUPPORT',
            'indicator_code' => 'jumlah_penduduk',
            'indicator_label' => 'Jumlah penduduk',
            'year' => 2021,
            'semester' => 1,
            'value' => 450,
            'evaluation_note' => null,
            'sort_order' => 1,
            'level' => 'kecamatan',
            'area_id' => $this->kecamatanA->id,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('kecamatan.pilot-project-keluarga-sehat.report'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_laporan_pdf_tetap_aman_saat_scope_metadata_tidak_sinkron(): void
    {
        $user = User::factory()->create(['scope' => 'desa', 'area_id' => $this->kecamatanB->id]);
        $user->assignRole('admin-desa');

        $response = $this->actingAs($user)->get(route('desa.pilot-project-keluarga-sehat.report'));

        $response->assertStatus(403);
    }
}
