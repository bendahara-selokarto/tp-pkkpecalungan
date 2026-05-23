<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Controllers;

use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\UseCases\ListScopedBukuKonsultasiesUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class BukuKonsultasiPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuKonsultasiesUseCase $listScopedBukuKonsultasiesUseCase,
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
        $this->authorize('viewAny', BukuKonsultasi::class);

        $items = $this->listScopedBukuKonsultasiesUseCase
            ->executeAll($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        
        $pdf = $this->pdfViewFactory->loadView('pdf.buku_konsultasi_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("buku-konsultasi-{$level}-report.pdf");
    }
}
