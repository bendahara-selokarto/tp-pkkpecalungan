<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\UseCases\ListScopedProgramPrioritasUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class ProgramPrioritasPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedProgramPrioritasUseCase $listScopedProgramPrioritasUseCase,
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
        $this->authorize('viewAny', ProgramPrioritas::class);

        $items = $this->listScopedProgramPrioritasUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.program_prioritas_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("program-prioritas-{$level}-report.pdf");
    }
}
