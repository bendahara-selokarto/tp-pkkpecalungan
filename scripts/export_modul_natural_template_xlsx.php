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
/** @var array<string, array<string, string>> $roleGroupModes */
$roleGroupModes = $ref->getReflectionConstant('ROLE_GROUP_MODES')?->getValue() ?? [];

$moduleLabels = [
    'anggota-tim-penggerak' => 'Buku Anggota Tim Penggerak',
    'anggota-tim-penggerak-kader' => 'Buku Anggota Tim Penggerak Kader',
    'kader-khusus' => 'Buku Kader Khusus',
    'agenda-surat' => 'Buku Agenda Surat',
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
    'paar' => 'Data PAAR',
    'data-pelatihan-kader' => 'Data Pelatihan Kader',
    'taman-bacaan' => 'Data Taman Bacaan',
    'koperasi' => 'Data Koperasi',
    'kejar-paket' => 'Data Kejar Paket',
    'data-keluarga' => 'Data Keluarga',
    'data-industri-rumah-tangga' => 'Data Industri Rumah Tangga',
    'data-pemanfaatan-tanah-pekarangan-hatinya-pkk' => 'Data HATINYA PKK',
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
$modules = [];
foreach ($groupModules as $moduleSlugs) {
    foreach ($moduleSlugs as $moduleSlug) {
        if (isset($seen[$moduleSlug])) {
            continue;
        }
        $seen[$moduleSlug] = true;
        $modules[] = [
            'slug' => $moduleSlug,
            'name' => $moduleLabels[$moduleSlug] ?? $moduleSlug,
        ];
    }
}

$roleColumns = [
    'kecamatan-sekretaris' => ['rw' => 3, 'ro' => 4],
    'kecamatan-pokja-i' => ['rw' => 5, 'ro' => 6],
    'kecamatan-pokja-ii' => ['rw' => 7, 'ro' => 8],
    'kecamatan-pokja-iii' => ['rw' => 9, 'ro' => 10],
    'kecamatan-pokja-iv' => ['rw' => 11, 'ro' => 12],
    'desa-sekretaris' => ['rw' => 13, 'ro' => 14],
    'desa-pokja-i' => ['rw' => 15, 'ro' => 16],
    'desa-pokja-ii' => ['rw' => 17, 'ro' => 18],
    'desa-pokja-iii' => ['rw' => 19, 'ro' => 20],
    'desa-pokja-iv' => ['rw' => 21, 'ro' => 22],
];

$modeReadWrite = RoleMenuVisibilityService::MODE_READ_WRITE;
$modeReadOnly = RoleMenuVisibilityService::MODE_READ_ONLY;

$moduleModesByRole = [];
foreach ($roleColumns as $role => $_columns) {
    $moduleModesByRole[$role] = [];
    foreach ($roleGroupModes[$role] ?? [] as $group => $mode) {
        foreach ($groupModules[$group] ?? [] as $moduleSlug) {
            $existing = $moduleModesByRole[$role][$moduleSlug] ?? null;
            if ($existing === $modeReadWrite) {
                continue;
            }

            if ($mode === $modeReadWrite || $existing === null) {
                $moduleModesByRole[$role][$moduleSlug] = $mode;
            }
        }
    }
}

function esc(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function colName(int $col): string
{
    $name = '';
    while ($col > 0) {
        $mod = ($col - 1) % 26;
        $name = chr(65 + $mod) . $name;
        $col = intdiv($col - 1, 26);
    }

    return $name;
}

function cellInline(int $col, int $row, string $text, int $style = 0): string
{
    $ref = colName($col) . $row;
    $sAttr = $style > 0 ? ' s="' . $style . '"' : '';

    return '<c r="' . $ref . '" t="inlineStr"' . $sAttr . '><is><t>' . esc($text) . '</t></is></c>';
}

function cellNumber(int $col, int $row, int $value, int $style = 0): string
{
    $ref = colName($col) . $row;
    $sAttr = $style > 0 ? ' s="' . $style . '"' : '';

    return '<c r="' . $ref . '"' . $sAttr . '><v>' . $value . '</v></c>';
}

$rowsXml = [];

$row1Cells = [
    cellInline(1, 1, 'No', 1),
    cellInline(2, 1, 'Nama Modul', 1),
    cellInline(3, 1, 'Kecamatan', 1),
    cellInline(13, 1, 'Desa', 1),
];
$rowsXml[] = '<row r="1">' . implode('', $row1Cells) . '</row>';

$row2Cells = [
    cellInline(3, 2, 'Sekretaris', 1),
    cellInline(5, 2, 'Pokja I', 1),
    cellInline(7, 2, 'Pokja II', 1),
    cellInline(9, 2, 'Pokja III', 1),
    cellInline(11, 2, 'Pokja IV', 1),
    cellInline(13, 2, 'Sekretaris', 1),
    cellInline(15, 2, 'Pokja I', 1),
    cellInline(17, 2, 'Pokja II', 1),
    cellInline(19, 2, 'Pokja III', 1),
    cellInline(21, 2, 'Pokja IV', 1),
];
$rowsXml[] = '<row r="2">' . implode('', $row2Cells) . '</row>';

$rwRoCells = [];
for ($col = 3; $col <= 22; $col++) {
    $rwRoCells[] = cellInline($col, 3, $col % 2 === 1 ? 'RW' : 'RO', 1);
}
$rowsXml[] = '<row r="3">' . implode('', $rwRoCells) . '</row>';

$dataRow = 4;
$no = 1;
foreach ($modules as $module) {
    $cells = [];
    for ($col = 1; $col <= 22; $col++) {
        if ($col === 1) {
            $cells[] = cellNumber(1, $dataRow, $no, 2);
            continue;
        }

        if ($col === 2) {
            $cells[] = cellInline(2, $dataRow, (string) $module['name'], 2);
            continue;
        }

        $marker = '';
        foreach ($roleColumns as $role => $columns) {
            $mode = $moduleModesByRole[$role][(string) $module['slug']] ?? null;
            if ($mode === $modeReadWrite && $col === $columns['rw']) {
                $marker = 'v';
                break;
            }
            if ($mode === $modeReadOnly && $col === $columns['ro']) {
                $marker = 'v';
                break;
            }
        }

        $cells[] = cellInline($col, $dataRow, $marker, 1);
    }
    $rowsXml[] = '<row r="' . $dataRow . '">' . implode('', $cells) . '</row>';
    $dataRow++;
    $no++;
}

$mergeRefs = [
    'A1:A3',
    'B1:B3',
    'C1:L1',
    'M1:V1',
    'C2:D2',
    'E2:F2',
    'G2:H2',
    'I2:J2',
    'K2:L2',
    'M2:N2',
    'O2:P2',
    'Q2:R2',
    'S2:T2',
    'U2:V2',
];
$mergeXml = '';
foreach ($mergeRefs as $refMerge) {
    $mergeXml .= '<mergeCell ref="' . $refMerge . '"/>';
}

$lastRow = max(3, $dataRow - 1);

$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
    . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<dimension ref="A1:V' . $lastRow . '"/>'
    . '<sheetViews><sheetView workbookViewId="0"/></sheetViews>'
    . '<sheetFormatPr defaultRowHeight="15"/>'
    . '<cols>'
    . '<col min="1" max="1" width="6" customWidth="1"/>'
    . '<col min="2" max="2" width="40" customWidth="1"/>'
    . '<col min="3" max="22" width="8" customWidth="1"/>'
    . '</cols>'
    . '<sheetData>' . implode('', $rowsXml) . '</sheetData>'
    . '<mergeCells count="' . count($mergeRefs) . '">' . $mergeXml . '</mergeCells>'
    . '</worksheet>';

$workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
    . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<sheets><sheet name="Daftar Modul" sheetId="1" r:id="rId1"/></sheets>'
    . '</workbook>';

$workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
    . '</Relationships>';

$rootRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
    . '</Relationships>';

$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
    . '<fills count="2">'
    . '<fill><patternFill patternType="none"/></fill>'
    . '<fill><patternFill patternType="gray125"/></fill>'
    . '</fills>'
    . '<borders count="2">'
    . '<border><left/><right/><top/><bottom/><diagonal/></border>'
    . '<border>'
    . '<left style="thin"><color auto="1"/></left>'
    . '<right style="thin"><color auto="1"/></right>'
    . '<top style="thin"><color auto="1"/></top>'
    . '<bottom style="thin"><color auto="1"/></bottom>'
    . '<diagonal/>'
    . '</border>'
    . '</borders>'
    . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
    . '<cellXfs count="3">'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
    . '<alignment horizontal="center" vertical="center" wrapText="1"/>'
    . '</xf>'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
    . '<alignment vertical="center" wrapText="1"/>'
    . '</xf>'
    . '</cellXfs>'
    . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
    . '</styleSheet>';

$contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
    . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
    . '</Types>';

$outputDir = $root . '/docs/process/exports';
if (! is_dir($outputDir) && ! mkdir($outputDir, 0777, true) && ! is_dir($outputDir)) {
    throw new RuntimeException('Gagal membuat folder output: ' . $outputDir);
}

$outputFile = $outputDir . '/FORMAT_MODUL_NATURAL_TERISI_2026_02_25.xlsx';
$zip = new ZipArchive();
$openResult = $zip->open($outputFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($openResult !== true) {
    throw new RuntimeException('Gagal membuat xlsx. ZipArchive code: ' . $openResult);
}

$zip->addFromString('[Content_Types].xml', $contentTypesXml);
$zip->addFromString('_rels/.rels', $rootRelsXml);
$zip->addFromString('xl/workbook.xml', $workbookXml);
$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
$zip->addFromString('xl/styles.xml', $stylesXml);
$zip->close();

echo $outputFile . PHP_EOL;
