<?php

namespace App\Domains\Wilayah\WarungPkk\Controllers;

use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\UseCases\ListScopedWarungPkkUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class WarungPkkPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedWarungPkkUseCase $listScopedWarungPkkUseCase,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
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
        $this->authorize('viewAny', WarungPkk::class);

        $items = $this->listScopedWarungPkkUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        $pdf = $this->pdfViewFactory->loadView('pdf.warung_pkk_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("warung-pkk-{$level}-report.pdf");
    }
}
