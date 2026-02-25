<?php

namespace App\Domains\Wilayah\AgendaSurat\Controllers;

use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\UseCases\ListScopedAgendaSuratUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class AgendaSuratReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedAgendaSuratUseCase $listScopedAgendaSuratUseCase,
        private readonly PdfViewFactory $pdfViewFactory
    ) {
    }

    public function printDesaReport(): Response
    {
        return $this->streamReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanReport(): Response
    {
        return $this->streamReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaEkspedisiReport(): Response
    {
        return $this->streamEkspedisiReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanEkspedisiReport(): Response
    {
        return $this->streamEkspedisiReport(ScopeLevel::KECAMATAN->value);
    }

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', AgendaSurat::class);

        $items = $this->listScopedAgendaSuratUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.agenda_surat_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("agenda-surat-{$level}-report.pdf");
    }

    private function streamEkspedisiReport(string $level): Response
    {
        $this->authorize('viewAny', AgendaSurat::class);

        $items = $this->listScopedAgendaSuratUseCase
            ->executeAll($level)
            ->where('jenis_surat', 'keluar')
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.ekspedisi_surat_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("ekspedisi-surat-{$level}-report.pdf");
    }
}
