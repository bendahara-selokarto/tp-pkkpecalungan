<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases\ListScopedPilotProjectKeluargaSehatUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class PilotProjectKeluargaSehatPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedPilotProjectKeluargaSehatUseCase $listUseCase,
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
        $this->authorize('viewAny', PilotProjectKeluargaSehatReport::class);

        $reports = $this->listUseCase->executeAll($level)
            ->load(['values' => fn ($query) => $query->orderBy('sort_order')])
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.pilot_project_keluarga_sehat_report', [
            'reports' => $reports,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'sections' => config('pilot_project_keluarga_sehat.sections', []),
        ]);

        return $pdf->stream("pilot-project-keluarga-sehat-{$level}-report.pdf");
    }
}

