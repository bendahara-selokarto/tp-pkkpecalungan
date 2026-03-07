<?php

namespace App\Domains\Wilayah\TamanBacaan\Controllers;

use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\UseCases\ListScopedTamanBacaanUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class TamanBacaanPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedTamanBacaanUseCase $listScopedTamanBacaanUseCase,
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
        $this->authorize('viewAny', TamanBacaan::class);

        $items = $this->listScopedTamanBacaanUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        $pdf = $this->pdfViewFactory->loadView('pdf.taman_bacaan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("taman-bacaan-{$level}-report.pdf");
    }
}
