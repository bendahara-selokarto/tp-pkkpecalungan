<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\LaporanTahunanPkk\Services\LaporanTahunanPkkDocxGenerator;
use App\Domains\Wilayah\LaporanTahunanPkk\UseCases\BuildLaporanTahunanPkkDocumentUseCase;
use App\Domains\Wilayah\LaporanTahunanPkk\UseCases\GetScopedLaporanTahunanPkkUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LaporanTahunanPkkPrintController extends Controller
{
    public function __construct(
        private readonly GetScopedLaporanTahunanPkkUseCase $getUseCase,
        private readonly BuildLaporanTahunanPkkDocumentUseCase $buildDocumentUseCase,
        private readonly LaporanTahunanPkkDocxGenerator $docxGenerator
    ) {
    }

    public function printDesaReport(int $id): Response
    {
        return $this->streamReport($id, ScopeLevel::DESA->value);
    }

    public function printKecamatanReport(int $id): Response
    {
        return $this->streamReport($id, ScopeLevel::KECAMATAN->value);
    }

    private function streamReport(int $id, string $level): Response
    {
        $report = $this->getUseCase->execute($id, $level);
        $this->authorize('view', $report);

        $documentData = $this->buildDocumentUseCase->execute($report);
        $areaName = (string) ($report->area?->name ?? auth()->user()?->area?->name ?? '-');
        $binary = $this->docxGenerator->generate(
            $report,
            $documentData['grouped_entries'],
            $documentData['bidang_labels'],
            $areaName
        );

        $filename = sprintf(
            'laporan-tahunan-pkk-%s-%d-%s.docx',
            $level,
            (int) $report->tahun_laporan,
            Str::slug($areaName !== '' ? $areaName : 'wilayah')
        );

        return response($binary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
