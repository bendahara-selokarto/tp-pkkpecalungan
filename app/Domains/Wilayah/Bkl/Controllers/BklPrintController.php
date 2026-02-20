<?php

namespace App\Domains\Wilayah\Bkl\Controllers;

use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\UseCases\ListScopedBklUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class BklPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBklUseCase $listScopedBklUseCase,
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
        $this->authorize('viewAny', Bkl::class);

        $items = $this->listScopedBklUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.bkl_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("bkl-{$level}-report.pdf");
    }
}
