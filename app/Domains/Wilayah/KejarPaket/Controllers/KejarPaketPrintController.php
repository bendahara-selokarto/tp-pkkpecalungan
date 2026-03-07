<?php

namespace App\Domains\Wilayah\KejarPaket\Controllers;

use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\UseCases\ListScopedKejarPaketUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class KejarPaketPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedKejarPaketUseCase $listScopedKejarPaketUseCase,
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
        $this->authorize('viewAny', KejarPaket::class);

        $items = $this->listScopedKejarPaketUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $tahunAnggaran = $this->activeBudgetYearContextService->resolveForUser($user);
        $pdf = $this->pdfViewFactory->loadView('pdf.kejar_paket_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("kejar-paket-{$level}-report.pdf");
    }
}




