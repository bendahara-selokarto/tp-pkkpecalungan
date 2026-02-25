<?php

namespace App\Domains\Wilayah\Paar\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\UseCases\ListScopedPaarUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class PaarPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedPaarUseCase $listScopedPaarUseCase,
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
        $this->authorize('viewAny', Paar::class);

        $items = $this->listScopedPaarUseCase
            ->executeAll($level)
            ->sortBy(static function (Paar $item): int {
                $position = array_search($item->indikator, Paar::indicatorKeys(), true);

                return is_int($position) ? $position : 999;
            })
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.paar_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'indicatorLabels' => Paar::INDICATORS,
        ]);

        return $pdf->stream("paar-{$level}-report.pdf");
    }
}
