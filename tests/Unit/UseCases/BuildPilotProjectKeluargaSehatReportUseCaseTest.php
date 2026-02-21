<?php

namespace Tests\Unit\UseCases;

use App\Domains\Wilayah\Models\Area;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatValue;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases\BuildPilotProjectKeluargaSehatReportUseCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuildPilotProjectKeluargaSehatReportUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function use_case_mengembalikan_nilai_terurut_dan_katalog_section(): void
    {
        $kecamatan = Area::create(['name' => 'Pecalungan', 'level' => 'kecamatan']);
        $desa = Area::create(['name' => 'Gombong', 'level' => 'desa', 'parent_id' => $kecamatan->id]);

        $user = User::factory()->create([
            'scope' => 'desa',
            'area_id' => $desa->id,
        ]);

        $report = PilotProjectKeluargaSehatReport::create([
            'judul_laporan' => 'Laporan Uji',
            'dasar_hukum' => null,
            'pendahuluan' => null,
            'maksud_tujuan' => null,
            'pelaksanaan' => null,
            'dokumentasi' => null,
            'penutup' => null,
            'tahun_awal' => 2021,
            'tahun_akhir' => 2021,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        PilotProjectKeluargaSehatValue::create([
            'report_id' => $report->id,
            'section' => 'data_dukung',
            'cluster_code' => 'SUPPORT',
            'indicator_code' => 'jumlah_keluarga',
            'indicator_label' => 'Jumlah keluarga',
            'year' => 2021,
            'semester' => 1,
            'value' => 80,
            'evaluation_note' => null,
            'sort_order' => 20,
            'level' => 'desa',
            'area_id' => $desa->id,
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
            'sort_order' => 10,
            'level' => 'desa',
            'area_id' => $desa->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user);
        $useCase = app(BuildPilotProjectKeluargaSehatReportUseCase::class);

        $result = $useCase->execute($report->id, 'desa');

        $this->assertSame('jumlah_penduduk', $result['values'][0]->indicator_code);
        $this->assertSame('jumlah_keluarga', $result['values'][1]->indicator_code);
        $this->assertSame('A. Data Dukung', $result['sections'][0]['label'] ?? null);
        $this->assertSame('pilot-project-keluarga-sehat', $result['module']['slug'] ?? null);
    }
}
