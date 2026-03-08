<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PdfRegionalIdentityDefaultsTest extends TestCase
{
    #[DataProvider('pdfViewProvider')]
    public function test_view_pdf_yang_meminta_identitas_wilayah_memakai_nilai_default(
        string $view,
        array $overrides,
        array $expectedTokens
    ): void {
        $html = view($view, array_merge($this->baseViewData(), $overrides))->render();
        $normalizedHtml = $this->normalizeText($html);

        foreach ($expectedTokens as $token) {
            $this->assertStringContainsString(
                $this->normalizeText($token),
                $normalizedHtml,
                sprintf('Token "%s" tidak ditemukan pada view %s.', $token, $view)
            );
        }
    }

    public static function pdfViewProvider(): array
    {
        $identityTokens = ['Pecalungan', 'Batang', 'Jawa Tengah'];

        return [
            'anggota_tim_penggerak' => [
                'pdf.anggota_tim_penggerak_report',
                ['items' => collect(), 'tahunAnggaran' => 2026],
                $identityTokens,
            ],
            'bkl' => [
                'pdf.bkl_report',
                ['items' => collect(), 'budgetYearLabel' => 2026],
                ['Pecalungan'],
            ],
            'bkr' => [
                'pdf.bkr_report',
                ['items' => collect(), 'budgetYearLabel' => 2026],
                ['Pecalungan'],
            ],
            'paar' => [
                'pdf.paar_report',
                ['items' => collect(), 'indicatorLabels' => [], 'budgetYearLabel' => 2026],
                ['Pecalungan'],
            ],
            'taman_bacaan' => [
                'pdf.taman_bacaan_report',
                ['items' => collect(), 'tahunAnggaran' => 2026],
                $identityTokens,
            ],
            'posyandu' => [
                'pdf.posyandu_report',
                ['items' => collect(), 'area' => null, 'budgetYearLabel' => 2026],
                $identityTokens,
            ],
            'catatan_tp_pkk_desa_kelurahan' => [
                'pdf.catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan_report',
                ['items' => collect(), 'tahun' => 2026],
                $identityTokens,
            ],
            'catatan_tp_pkk_kecamatan' => [
                'pdf.catatan_data_kegiatan_warga_tp_pkk_kecamatan_report',
                ['items' => collect(), 'tahun' => 2026],
                $identityTokens,
            ],
            'catatan_tp_pkk_kabupaten_kota' => [
                'pdf.catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota_report',
                ['items' => collect(), 'tahun' => 2026],
                $identityTokens,
            ],
            'catatan_tp_pkk_provinsi' => [
                'pdf.catatan_data_kegiatan_warga_tp_pkk_provinsi_report',
                ['items' => collect(), 'tahun' => 2026],
                ['Jawa Tengah'],
            ],
            'data_umum_pkk' => [
                'pdf.data_umum_pkk_report',
                [
                    'items' => collect(),
                    'totals' => [],
                    'tpPkkDesaKelurahanTotals' => [],
                    'tahun' => 2026,
                ],
                $identityTokens,
            ],
            'data_umum_pkk_kecamatan' => [
                'pdf.data_umum_pkk_kecamatan_report',
                [
                    'items' => collect(),
                    'totals' => [],
                    'tpPkkKecamatanTotals' => [],
                    'tahun' => 2026,
                ],
                $identityTokens,
            ],
            'rekap_ibu_hamil_pkk_rt' => [
                'pdf.rekap_ibu_hamil_melahirkan_pkk_rt_report',
                ['items' => collect(), 'totals' => [], 'bulan' => 'Maret', 'tahun' => 2026],
                $identityTokens,
            ],
            'rekap_ibu_hamil_pkk_rw' => [
                'pdf.rekap_ibu_hamil_melahirkan_pkk_rw_report',
                ['items' => collect(), 'totals' => [], 'bulan' => 'Maret', 'tahun' => 2026],
                $identityTokens,
            ],
            'rekap_ibu_hamil_dusun_lingkungan' => [
                'pdf.rekap_ibu_hamil_melahirkan_dusun_lingkungan_report',
                ['items' => collect(), 'totals' => [], 'bulan' => 'Maret', 'tahun' => 2026],
                $identityTokens,
            ],
            'rekap_ibu_hamil_tp_pkk_kecamatan' => [
                'pdf.rekap_ibu_hamil_melahirkan_tp_pkk_kecamatan_report',
                ['items' => collect(), 'totals' => [], 'bulan' => 'Maret', 'tahun' => 2026],
                $identityTokens,
            ],
        ];
    }

    private function baseViewData(): array
    {
        return [
            'level' => 'desa',
            'areaName' => 'Contoh Area',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ];
    }

    private function normalizeText(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', strtoupper(strip_tags($text))));
    }
}
