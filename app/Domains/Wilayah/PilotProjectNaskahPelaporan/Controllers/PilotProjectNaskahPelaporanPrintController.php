<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases\ListScopedPilotProjectNaskahPelaporanUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class PilotProjectNaskahPelaporanPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedPilotProjectNaskahPelaporanUseCase $listUseCase,
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

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', PilotProjectNaskahPelaporanReport::class);

        $reports = $this->listUseCase->execute($level)
            ->load(['attachments' => fn ($query) => $query->orderBy('category')->orderBy('id')])
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.pilot_project_naskah_pelaporan_report', [
            'reports' => $reports,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'categoryLabels' => config('pilot_project_naskah_pelaporan.attachment_categories', []),
        ], PdfViewFactory::ORIENTATION_PORTRAIT);

        return $pdf->stream("pilot-project-naskah-pelaporan-{$level}-report.pdf");
    }
}
