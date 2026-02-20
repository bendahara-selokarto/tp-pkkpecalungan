<?php

namespace App\Domains\Wilayah\Bkr\Controllers;

use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\UseCases\ListScopedBkrUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class BkrPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBkrUseCase $listScopedBkrUseCase
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
        $this->authorize('viewAny', Bkr::class);

        $items = $this->listScopedBkrUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = Pdf::loadView('pdf.bkr_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("bkr-{$level}-report.pdf");
    }
}

