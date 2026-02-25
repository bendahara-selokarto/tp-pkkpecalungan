<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Services;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use RuntimeException;
use ZipArchive;

class LaporanTahunanPkkDocxGenerator
{
    private const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    public function generate(
        LaporanTahunanPkkReport $report,
        array $groupedEntries,
        array $bidangLabels,
        string $areaName
    ): string {
        $tmpPath = $this->prepareWorkingDocx();

        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            @unlink($tmpPath);
            throw new RuntimeException('Gagal membuka arsip .docx.');
        }

        $documentXml = $zip->getFromName('word/document.xml');
        if (! is_string($documentXml) || $documentXml === '') {
            $zip->close();
            @unlink($tmpPath);
            throw new RuntimeException('word/document.xml tidak ditemukan pada template.');
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($documentXml);

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', self::W_NS);

        /** @var DOMElement|null $body */
        $body = $xpath->query('/w:document/w:body')->item(0);
        if (! $body instanceof DOMElement) {
            $zip->close();
            @unlink($tmpPath);
            throw new RuntimeException('Node body dokumen .docx tidak valid.');
        }

        $sectPr = $xpath->query('./w:sectPr', $body)->item(0);
        $sectPrClone = $sectPr instanceof DOMElement ? $sectPr->cloneNode(true) : null;

        while ($body->firstChild) {
            $body->removeChild($body->firstChild);
        }

        $scopeTitle = $report->level === ScopeLevel::DESA->value ? 'DESA' : 'KEC.';
        $this->appendParagraph($dom, $body, 'LAPORAN TAHUNAN', true, 'center', 30);
        $this->appendParagraph(
            $dom,
            $body,
            sprintf('TIM PENGGERAK PKK %s %s', $scopeTitle, strtoupper($areaName)),
            true,
            'center',
            24
        );
        $this->appendParagraph($dom, $body, sprintf('TAHUN %d', (int) $report->tahun_laporan), true, 'center', 24);
        $this->appendBlankParagraph($dom, $body);
        $this->appendParagraph($dom, $body, 'DISUSUN OLEH :', false, 'left');
        $this->appendParagraph(
            $dom,
            $body,
            (string) ($report->disusun_oleh ?: sprintf('TIM PENGGERAK PKK %s %s', $scopeTitle, strtoupper($areaName))),
            true,
            'left'
        );

        $this->appendBlankParagraph($dom, $body);
        $this->appendParagraph($dom, $body, 'PENDAHULUAN', true, 'left', 24);
        $this->appendTextBlock($dom, $body, (string) ($report->pendahuluan ?: ''));

        $this->appendBlankParagraph($dom, $body);
        $this->appendParagraph($dom, $body, 'PELAKSANAAN KEGIATAN', true, 'left', 24);
        $this->appendParagraph($dom, $body, 'KEGIATAN SECARA UMUM', true, 'left');

        foreach (config('laporan_tahunan_pkk.bidang_options', []) as $bidang) {
            $label = (string) ($bidangLabels[$bidang] ?? strtoupper((string) $bidang));
            $rows = is_array($groupedEntries[$bidang] ?? null) ? $groupedEntries[$bidang] : [];
            $this->appendBlankParagraph($dom, $body);
            $this->appendParagraph($dom, $body, $label, true, 'left');
            $this->appendKegiatanTable($dom, $body, $rows);
        }

        $this->appendBlankParagraph($dom, $body);
        $this->appendParagraph($dom, $body, 'KEBERHASILAN', true, 'left', 24);
        $this->appendTextBlock($dom, $body, (string) ($report->keberhasilan ?: ''));
        $this->appendBlankParagraph($dom, $body);

        $this->appendParagraph($dom, $body, 'HAMBATAN', true, 'left', 24);
        $this->appendTextBlock($dom, $body, (string) ($report->hambatan ?: ''));
        $this->appendBlankParagraph($dom, $body);

        $this->appendParagraph($dom, $body, 'KESIMPULAN', true, 'left', 24);
        $this->appendTextBlock($dom, $body, (string) ($report->kesimpulan ?: ''));
        $this->appendBlankParagraph($dom, $body);

        $this->appendParagraph($dom, $body, 'PENUTUP', true, 'left', 24);
        $this->appendTextBlock($dom, $body, (string) ($report->penutup ?: ''));
        $this->appendBlankParagraph($dom, $body);
        $this->appendBlankParagraph($dom, $body);

        $this->appendParagraph(
            $dom,
            $body,
            (string) ($report->jabatan_penanda_tangan ?: sprintf('Ketua TP. PKK %s %s', $scopeTitle, strtoupper($areaName))),
            false,
            'right'
        );
        $this->appendBlankParagraph($dom, $body);
        $this->appendBlankParagraph($dom, $body);
        $this->appendParagraph($dom, $body, (string) ($report->nama_penanda_tangan ?: '-'), true, 'right');

        if ($sectPrClone instanceof DOMElement) {
            $body->appendChild($sectPrClone);
        }

        $zip->addFromString('word/document.xml', $dom->saveXML());
        $zip->close();

        $binary = file_get_contents($tmpPath);
        @unlink($tmpPath);

        if (! is_string($binary) || $binary === '') {
            throw new RuntimeException('Gagal membuat output .docx laporan tahunan.');
        }

        return $binary;
    }

    private function prepareWorkingDocx(): string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'laporan_tahunan_pkk_');
        if (! is_string($tmpPath) || $tmpPath === '') {
            throw new RuntimeException('Gagal membuat file sementara untuk generator .docx.');
        }

        $templatePath = base_path('docs/referensi/LAPORAN TAHUNAN PKK th 2025.docx');
        if (is_file($templatePath)) {
            if (! copy($templatePath, $tmpPath)) {
                @unlink($tmpPath);
                throw new RuntimeException('Gagal menyalin template laporan tahunan .docx.');
            }

            return $tmpPath;
        }

        $this->createMinimalDocxPackage($tmpPath);

        return $tmpPath;
    }

    private function createMinimalDocxPackage(string $path): void
    {
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat arsip .docx fallback.');
        }

        $zip->addFromString('[Content_Types].xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
XML);
        $zip->addFromString('_rels/.rels', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
XML);
        $zip->addFromString('word/document.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    <w:sectPr/>
  </w:body>
</w:document>
XML);
        $zip->close();
    }

    private function appendBlankParagraph(DOMDocument $dom, DOMElement $parent): void
    {
        $this->appendParagraph($dom, $parent, '', false, 'left');
    }

    private function appendTextBlock(DOMDocument $dom, DOMElement $parent, string $text): void
    {
        $lines = preg_split('/\R/u', $text) ?: [];
        if ($lines === []) {
            $this->appendParagraph($dom, $parent, '-', false, 'left');
            return;
        }

        foreach ($lines as $line) {
            $normalized = trim((string) $line);
            $this->appendParagraph($dom, $parent, $normalized === '' ? '-' : $normalized, false, 'left');
        }
    }

    private function appendParagraph(
        DOMDocument $dom,
        DOMElement $parent,
        string $text,
        bool $bold = false,
        string $align = 'left',
        int $fontSizeHalfPoint = 22
    ): void {
        $paragraph = $dom->createElementNS(self::W_NS, 'w:p');
        $pPr = $dom->createElementNS(self::W_NS, 'w:pPr');
        $jc = $dom->createElementNS(self::W_NS, 'w:jc');
        $jc->setAttributeNS(self::W_NS, 'w:val', $align);
        $pPr->appendChild($jc);
        $paragraph->appendChild($pPr);

        $run = $dom->createElementNS(self::W_NS, 'w:r');
        $rPr = $dom->createElementNS(self::W_NS, 'w:rPr');

        if ($bold) {
            $rPr->appendChild($dom->createElementNS(self::W_NS, 'w:b'));
        }

        $sz = $dom->createElementNS(self::W_NS, 'w:sz');
        $sz->setAttributeNS(self::W_NS, 'w:val', (string) $fontSizeHalfPoint);
        $rPr->appendChild($sz);
        $szCs = $dom->createElementNS(self::W_NS, 'w:szCs');
        $szCs->setAttributeNS(self::W_NS, 'w:val', (string) $fontSizeHalfPoint);
        $rPr->appendChild($szCs);
        $run->appendChild($rPr);

        $textNode = $dom->createElementNS(self::W_NS, 'w:t');
        $textNode->setAttribute('xml:space', 'preserve');
        $textNode->appendChild($dom->createTextNode($text));
        $run->appendChild($textNode);
        $paragraph->appendChild($run);

        $parent->appendChild($paragraph);
    }

    private function appendKegiatanTable(DOMDocument $dom, DOMElement $parent, array $rows): void
    {
        $table = $dom->createElementNS(self::W_NS, 'w:tbl');
        $tblPr = $dom->createElementNS(self::W_NS, 'w:tblPr');
        $tblBorders = $dom->createElementNS(self::W_NS, 'w:tblBorders');
        foreach (['top', 'left', 'bottom', 'right', 'insideH', 'insideV'] as $edge) {
            $border = $dom->createElementNS(self::W_NS, "w:$edge");
            $border->setAttributeNS(self::W_NS, 'w:val', 'nil');
            $tblBorders->appendChild($border);
        }
        $tblPr->appendChild($tblBorders);
        $table->appendChild($tblPr);

        if ($rows === []) {
            $rows = [[
                'activity_date' => null,
                'description' => 'Belum ada data kegiatan.',
            ]];
        }

        $number = 1;
        foreach ($rows as $row) {
            $dateValue = '-';
            if (is_string($row['activity_date'] ?? null) && $row['activity_date'] !== '') {
                try {
                    $dateValue = Carbon::parse($row['activity_date'])->translatedFormat('j F Y');
                } catch (\Throwable) {
                    $dateValue = (string) $row['activity_date'];
                }
            }

            $description = trim((string) ($row['description'] ?? ''));
            if ($description === '') {
                $description = '-';
            }

            $tr = $dom->createElementNS(self::W_NS, 'w:tr');
            $this->appendTableCell($dom, $tr, (string) $number, 'center');
            $this->appendTableCell($dom, $tr, $dateValue, 'center');
            $this->appendTableCell($dom, $tr, ':', 'center');
            $this->appendTableCell($dom, $tr, $description, 'left');
            $table->appendChild($tr);
            $number++;
        }

        $parent->appendChild($table);
    }

    private function appendTableCell(
        DOMDocument $dom,
        DOMElement $row,
        string $text,
        string $align
    ): void {
        $cell = $dom->createElementNS(self::W_NS, 'w:tc');
        $cellPr = $dom->createElementNS(self::W_NS, 'w:tcPr');
        $cell->appendChild($cellPr);

        $paragraph = $dom->createElementNS(self::W_NS, 'w:p');
        $pPr = $dom->createElementNS(self::W_NS, 'w:pPr');
        $jc = $dom->createElementNS(self::W_NS, 'w:jc');
        $jc->setAttributeNS(self::W_NS, 'w:val', $align);
        $pPr->appendChild($jc);
        $paragraph->appendChild($pPr);

        $run = $dom->createElementNS(self::W_NS, 'w:r');
        $textNode = $dom->createElementNS(self::W_NS, 'w:t');
        $textNode->setAttribute('xml:space', 'preserve');
        $textNode->appendChild($dom->createTextNode($text));
        $run->appendChild($textNode);
        $paragraph->appendChild($run);
        $cell->appendChild($paragraph);
        $row->appendChild($cell);
    }
}
