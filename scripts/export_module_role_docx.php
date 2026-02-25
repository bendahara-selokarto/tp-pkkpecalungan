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

$groupLabels = [
    'sekretaris-tpk' => 'Sekretaris TPK',
    'pokja-i' => 'Pokja I',
    'pokja-ii' => 'Pokja II',
    'pokja-iii' => 'Pokja III',
    'pokja-iv' => 'Pokja IV',
    'monitoring' => 'Monitoring',
];

$roleLabels = [
    'desa-sekretaris' => 'Sekretaris Desa',
    'kecamatan-sekretaris' => 'Sekretaris Kecamatan',
    'desa-pokja-i' => 'Pokja I Desa',
    'desa-pokja-ii' => 'Pokja II Desa',
    'desa-pokja-iii' => 'Pokja III Desa',
    'desa-pokja-iv' => 'Pokja IV Desa',
    'kecamatan-pokja-i' => 'Pokja I Kecamatan',
    'kecamatan-pokja-ii' => 'Pokja II Kecamatan',
    'kecamatan-pokja-iii' => 'Pokja III Kecamatan',
    'kecamatan-pokja-iv' => 'Pokja IV Kecamatan',
    'admin-desa' => 'Admin Desa (Legacy)',
    'admin-kecamatan' => 'Admin Kecamatan (Legacy)',
    'super-admin' => 'Super Admin (Teknis)',
];

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

$ownersByModule = [];
$viewersByModule = [];

foreach ($roleGroupModes as $role => $groupModes) {
    foreach ($groupModes as $group => $mode) {
        foreach ($groupModules[$group] ?? [] as $moduleSlug) {
            if ($mode === RoleMenuVisibilityService::MODE_READ_WRITE) {
                $ownersByModule[$moduleSlug][$role] = true;
            } elseif ($mode === RoleMenuVisibilityService::MODE_READ_ONLY) {
                $viewersByModule[$moduleSlug][$role] = true;
            }
        }
    }
}

$rows = [];
$seen = [];
$no = 1;
foreach ($groupModules as $groupSlug => $moduleSlugs) {
    foreach ($moduleSlugs as $moduleSlug) {
        if (isset($seen[$moduleSlug])) {
            continue;
        }
        $seen[$moduleSlug] = true;

        $owners = array_keys($ownersByModule[$moduleSlug] ?? []);
        $viewers = array_keys($viewersByModule[$moduleSlug] ?? []);

        $ownerLabels = array_map(
            static fn (string $role): string => $roleLabels[$role] ?? $role,
            $owners
        );
        $viewerLabels = array_map(
            static fn (string $role): string => $roleLabels[$role] ?? $role,
            $viewers
        );

        sort($ownerLabels);
        sort($viewerLabels);

        $rows[] = [
            (string) $no,
            $groupLabels[$groupSlug] ?? $groupSlug,
            $moduleLabels[$moduleSlug] ?? $moduleSlug,
            $moduleSlug,
            $ownerLabels === [] ? '-' : implode(', ', $ownerLabels),
            $viewerLabels === [] ? '-' : implode(', ', $viewerLabels),
            '[ ] Sesuai  [ ] Perlu Perbaikan',
            '',
        ];
        $no++;
    }
}

function xmlEscape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function wParagraph(string $text, bool $bold = false): string
{
    $parts = preg_split("/\r\n|\r|\n/", $text) ?: [''];
    $runs = [];
    $total = count($parts);

    foreach ($parts as $index => $part) {
        $runProps = $bold ? '<w:rPr><w:b/></w:rPr>' : '';
        $runs[] = '<w:r>' . $runProps . '<w:t xml:space="preserve">' . xmlEscape($part) . '</w:t></w:r>';
        if ($index < $total - 1) {
            $runs[] = '<w:r><w:br/></w:r>';
        }
    }

    return '<w:p>' . implode('', $runs) . '</w:p>';
}

function wCell(string $text, bool $bold = false): string
{
    return '<w:tc><w:tcPr><w:tcW w:w="0" w:type="auto"/></w:tcPr>' . wParagraph($text, $bold) . '</w:tc>';
}

$header = [
    'No',
    'Kelompok Modul',
    'Nama Modul (Bahasa User)',
    'Kode Modul (Teknis)',
    'Role Pemilik (Akses Penuh)',
    'Role Pantau (Read Only)',
    'Checklist Kesesuaian',
    'Catatan Perbaikan Role',
];

$tableRows = [];
$tableRows[] = '<w:tr>' . implode('', array_map(static fn (string $h): string => wCell($h, true), $header)) . '</w:tr>';

foreach ($rows as $row) {
    $tableRows[] = '<w:tr>' . implode('', array_map(static fn (string $c): string => wCell($c), $row)) . '</w:tr>';
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
    . implode('', $tableRows)
    . '</w:tbl>';

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
    . '<w:body>'
    . wParagraph('CHECKLIST MODUL SISTEM DAN KESESUAIAN ROLE', true)
    . wParagraph('Dokumen ini dipakai untuk diskusi dengan user domain agar penempatan modul sesuai role kerja di lapangan.')
    . wParagraph('Petunjuk isi: pilih "Sesuai" jika modul sudah tepat, atau "Perlu Perbaikan" lalu isi catatan role yang benar.')
    . wParagraph('Sumber data: konfigurasi akses modul pada aplikasi (RoleMenuVisibilityService).')
    . $table
    . '<w:p/>'
    . wParagraph('Catatan penting:', true)
    . wParagraph('1) Role "Admin Desa/Kecamatan" ditandai legacy.')
    . wParagraph('2) "Super Admin" adalah role teknis untuk pengelolaan sistem.')
    . wParagraph('3) Fokus verifikasi domain ada pada role operasional Desa/Kecamatan.')
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

$outputFile = $outputDir . '/CHECKLIST_AUDIT_MODUL_ROLE_2026_02_25.docx';
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
