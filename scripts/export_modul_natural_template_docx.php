<?php

declare(strict_types=1);

use App\Domains\Wilayah\Services\RoleMenuVisibilityService;

$root = dirname(__DIR__);
$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    require_once $autoload;
}

if (! class_exists(RoleMenuVisibilityService::class)) {
    require_once $root . '/app/Domains/Wilayah/Services/RoleMenuVisibilityService.php';
}

$ref = new ReflectionClass(RoleMenuVisibilityService::class);
/** @var array<string, list<string>> $groupModules */
$groupModules = $ref->getReflectionConstant('GROUP_MODULES')?->getValue() ?? [];

$moduleLabels = [
    'anggota-tim-penggerak' => 'Buku Daftar Anggota Tim Penggerak PKK',
    'anggota-tim-penggerak-kader' => 'Buku Daftar Anggota TP PKK dan Kader',
    'kader-khusus' => 'Buku Kader Khusus',
    'agenda-surat' => 'Buku Agenda Surat Masuk/Keluar',
    'buku-keuangan' => 'Buku Keuangan',
    'bantuans' => 'Buku Bantuan',
    'inventaris' => 'Buku Inventaris',
    'activities' => 'Buku Kegiatan',
    'anggota-pokja' => 'Daftar Anggota Pokja',
    'prestasi-lomba' => 'Buku Prestasi/Lomba',
    'laporan-tahunan-pkk' => 'Laporan Tahunan PKK',
    'data-warga' => 'Data Warga',
    'data-kegiatan-warga' => 'Data Kegiatan Warga',
    'bkl' => 'Rekap Kelompok BKL',
    'bkr' => 'Rekap Kelompok BKR',
    'paar' => 'Buku PAAR',
    'data-pelatihan-kader' => 'Data Pelatihan Kader',
    'taman-bacaan' => 'Data Taman Bacaan',
    'koperasi' => 'Data Koperasi',
    'kejar-paket' => 'Data Kejar Paket',
    'data-keluarga' => 'Data Keluarga',
    'data-industri-rumah-tangga' => 'Buku Industri Rumah Tangga',
    'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'Buku HATINYA PKK',
    'warung-pkk' => 'Data Warung PKK',
    'posyandu' => 'Data Posyandu',
    'simulasi-penyuluhan' => 'Kelompok Simulasi/Penyuluhan',
    'catatan-keluarga' => 'Catatan Keluarga',
    'program-prioritas' => 'Program Prioritas',
    'pilot-project-naskah-pelaporan' => 'Pilot Project Naskah Pelaporan',
    'pilot-project-keluarga-sehat' => 'Pilot Project Keluarga Sehat',
    'desa-activities' => 'Monitoring Kegiatan Desa',
];

$seen = [];
$rows = [];
$no = 1;
foreach ($groupModules as $moduleSlugs) {
    foreach ($moduleSlugs as $moduleSlug) {
        if (isset($seen[$moduleSlug])) {
            continue;
        }
        $seen[$moduleSlug] = true;
        $rows[] = [
            (string) $no,
            $moduleLabels[$moduleSlug] ?? $moduleSlug,
        ];
        $no++;
    }
}

function xmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function paragraph(string $text, bool $bold = false): string
{
    $runProps = $bold ? '<w:rPr><w:b/></w:rPr>' : '';

    return '<w:p><w:r>' . $runProps . '<w:t xml:space="preserve">' . xmlEscape($text) . '</w:t></w:r></w:p>';
}

/**
 * @param array{gridSpan?: int, vMerge?: "restart"|"continue"} $opts
 */
function cell(string $text, array $opts = [], bool $bold = false): string
{
    $tcPr = '<w:tcPr><w:tcW w:w="0" w:type="auto"/>';
    if (isset($opts['gridSpan']) && $opts['gridSpan'] > 1) {
        $tcPr .= '<w:gridSpan w:val="' . (int) $opts['gridSpan'] . '"/>';
    }
    if (($opts['vMerge'] ?? null) === 'restart') {
        $tcPr .= '<w:vMerge w:val="restart"/>';
    } elseif (($opts['vMerge'] ?? null) === 'continue') {
        $tcPr .= '<w:vMerge/>';
    }
    $tcPr .= '</w:tcPr>';

    return '<w:tc>' . $tcPr . paragraph($text, $bold) . '</w:tc>';
}

$row1 = '<w:tr>'
    . cell('No', ['vMerge' => 'restart'], true)
    . cell('Nama Modul', ['vMerge' => 'restart'], true)
    . cell('Kecamatan', ['gridSpan' => 10], true)
    . cell('Desa', ['gridSpan' => 10], true)
    . '</w:tr>';

$kecamatanGroups = ['Sekretaris', 'Pokja I', 'Pokja II', 'Pokja III', 'Pokja IV'];
$desaGroups = ['Sekretaris', 'Pokja I', 'Pokja II', 'Pokja III', 'Pokja IV'];

$row2 = '<w:tr>'
    . cell('', ['vMerge' => 'continue'])
    . cell('', ['vMerge' => 'continue']);
foreach ($kecamatanGroups as $label) {
    $row2 .= cell($label, ['gridSpan' => 2], true);
}
foreach ($desaGroups as $label) {
    $row2 .= cell($label, ['gridSpan' => 2], true);
}
$row2 .= '</w:tr>';

$row3 = '<w:tr>'
    . cell('', ['vMerge' => 'continue'])
    . cell('', ['vMerge' => 'continue']);
for ($i = 0; $i < 10; $i++) {
    $row3 .= cell($i % 2 === 0 ? 'RW' : 'RO', [], true);
}
for ($i = 0; $i < 10; $i++) {
    $row3 .= cell($i % 2 === 0 ? 'RW' : 'RO', [], true);
}
$row3 .= '</w:tr>';

$dataRows = [];
foreach ($rows as $entry) {
    $tr = '<w:tr>';
    $tr .= cell($entry[0]);
    $tr .= cell($entry[1]);
    for ($i = 0; $i < 20; $i++) {
        $tr .= cell('');
    }
    $tr .= '</w:tr>';
    $dataRows[] = $tr;
}

$table = '<w:tbl>'
    . '<w:tblPr>'
    . '<w:tblW w:w="0" w:type="auto"/>'
    . '<w:tblBorders>'
    . '<w:top w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
    . '<w:left w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
    . '<w:bottom w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
    . '<w:right w:val="single" w:sz="8" w:space="0" w:color="000000"/>'
    . '<w:insideH w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
    . '<w:insideV w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
    . '</w:tblBorders>'
    . '</w:tblPr>'
    . $row1
    . $row2
    . $row3
    . implode('', $dataRows)
    . '</w:tbl>';

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:body>'
    . paragraph('DAFTAR MODUL SISTEM (FORMAT USER DOMAIN)', true)
    . $table
    . '<w:sectPr>'
    . '<w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>'
    . '<w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720" w:header="708" w:footer="708" w:gutter="0"/>'
    . '</w:sectPr>'
    . '</w:body>'
    . '</w:document>';

$contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
    . '</Types>';

$relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
    . '</Relationships>';

$outputDir = $root . '/docs/process/exports';
if (! is_dir($outputDir) && ! mkdir($outputDir, 0777, true) && ! is_dir($outputDir)) {
    throw new RuntimeException('Gagal membuat folder output: ' . $outputDir);
}

$outputFile = $outputDir . '/FORMAT_MODUL_NATURAL_2026_02_25.docx';
$zip = new ZipArchive();
$openResult = $zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($openResult !== true) {
    throw new RuntimeException('Gagal membuat docx. ZipArchive code: ' . $openResult);
}

$zip->addFromString('[Content_Types].xml', $contentTypesXml);
$zip->addFromString('_rels/.rels', $relsXml);
$zip->addFromString('word/document.xml', $documentXml);
$zip->close();

echo $outputFile . PHP_EOL;
