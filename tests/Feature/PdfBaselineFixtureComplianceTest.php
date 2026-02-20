<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PdfBaselineFixtureComplianceTest extends TestCase
{
    private const FIXTURE_GLOB = __DIR__ . '/../Fixtures/pdf-baseline/*.json';

    #[DataProvider('baselineFixtureProvider')]
    public function test_fixture_pdf_baseline_menjaga_judul_dan_header(string $fixturePath): void
    {
        $fixture = $this->loadFixture($fixturePath);
        $html = view($fixture['view'], $this->buildViewData($fixture['view']))->render();
        $normalizedHtml = $this->normalizeText($html);

        $this->assertStringContainsString(
            $this->normalizeText($fixture['titleToken']),
            $normalizedHtml,
            sprintf('Token judul tidak ditemukan pada view %s.', $fixture['view'])
        );

        $cursor = 0;
        foreach ($fixture['expectedHeaderOrder'] as $header) {
            $needle = $this->normalizeText($header);
            $position = strpos($normalizedHtml, $needle, $cursor);

            $this->assertNotFalse(
                $position,
                sprintf('Header "%s" tidak ditemukan/urutannya berubah pada view %s.', $header, $fixture['view'])
            );

            $cursor = $position + strlen($needle);
        }
    }

    public function test_fixture_pdf_baseline_mencakup_semua_modul_pedoman_4_9_sampai_4_15(): void
    {
        $fixtures = $this->loadAllFixtures();
        $this->assertCount(19, $fixtures);

        $moduleSlugs = array_column($fixtures, 'moduleSlug');
        sort($moduleSlugs);

        $expected = [
            'agenda-surat',
            'anggota-tim-penggerak',
            'buku-keuangan',
            'catatan-keluarga',
            'data-industri-rumah-tangga',
            'data-kegiatan-warga',
            'data-keluarga',
            'data-pelatihan-kader',
            'data-pemanfaatan-tanah-pekarangan-hatinya-pkk',
            'data-warga',
            'inventaris',
            'kader-khusus',
            'kegiatan',
            'kejar-paket',
            'koperasi',
            'posyandu',
            'simulasi-penyuluhan',
            'taman-bacaan',
            'warung-pkk',
        ];
        sort($expected);

        $this->assertSame($expected, $moduleSlugs);
    }

    public static function baselineFixtureProvider(): array
    {
        $files = glob(self::FIXTURE_GLOB) ?: [];
        sort($files);

        return array_map(
            static fn (string $path): array => [$path],
            $files
        );
    }

    /**
     * @return array{level:string,areaName:string,printedBy:object,printedAt:\Illuminate\Support\Carbon,items?:\Illuminate\Support\Collection<int,mixed>,entries?:array<int,mixed>,activity?:object}
     */
    private function buildViewData(string $view): array
    {
        $base = [
            'level' => 'desa',
            'areaName' => 'Contoh Area',
            'printedBy' => (object) ['name' => 'System Test'],
            'printedAt' => now(),
        ];

        if ($view === 'pdf.buku_keuangan_report') {
            return array_merge($base, ['entries' => []]);
        }

        if ($view === 'pdf.activity') {
            return array_merge($base, [
                'activity' => (object) [
                    'id' => 1,
                    'level' => 'desa',
                    'area' => (object) ['name' => 'Contoh Area'],
                    'creator' => (object) ['name' => 'System Test'],
                    'nama_petugas' => 'Petugas Contoh',
                    'title' => 'Kegiatan Contoh',
                    'jabatan_petugas' => 'Sekretaris',
                    'activity_date' => now()->format('Y-m-d'),
                    'tempat_kegiatan' => 'Balai Desa',
                    'uraian' => 'Uraian kegiatan',
                    'description' => 'Uraian kegiatan',
                    'tanda_tangan' => 'Petugas Contoh',
                ],
            ]);
        }

        return array_merge($base, ['items' => collect()]);
    }

    /**
     * @return array{fixtureVersion:int,lampiran:string,moduleSlug:string,view:string,titleToken:string,defaultOrientation:string,expectedHeaderOrder:array<int,string>}
     */
    private function loadFixture(string $path): array
    {
        $raw = file_get_contents($path);
        $clean = ltrim((string) $raw, "\xEF\xBB\xBF");
        $decoded = json_decode($clean, true);

        $this->assertIsArray($decoded, sprintf('Fixture %s tidak valid JSON.', $path));

        foreach (['lampiran', 'moduleSlug', 'view', 'titleToken', 'defaultOrientation', 'expectedHeaderOrder'] as $key) {
            $this->assertArrayHasKey($key, $decoded, sprintf('Fixture %s tidak memiliki key %s.', $path, $key));
        }

        $this->assertIsArray($decoded['expectedHeaderOrder']);

        return $decoded;
    }

    /**
     * @return array<int, array{fixtureVersion:int,lampiran:string,moduleSlug:string,view:string,titleToken:string,defaultOrientation:string,expectedHeaderOrder:array<int,string>}>
     */
    private function loadAllFixtures(): array
    {
        $files = glob(self::FIXTURE_GLOB) ?: [];
        sort($files);

        return array_map(fn (string $path): array => $this->loadFixture($path), $files);
    }

    private function normalizeText(string $text): string
    {
        $stripped = strip_tags($text);
        $upper = strtoupper($stripped);

        return trim((string) preg_replace('/\s+/u', ' ', $upper));
    }
}
