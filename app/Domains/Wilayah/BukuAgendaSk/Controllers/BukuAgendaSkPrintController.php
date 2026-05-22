<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Controllers;

use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\UseCases\ListScopedBukuAgendaSkUseCase;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BukuAgendaSkPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuAgendaSkUseCase $listScopedBukuAgendaSkUseCase
    ) {
    }

    public function report(Request $request, string $level)
    {
        $this->authorize('print', BukuAgendaSk::class);

        $user = $request->user();
        $tahunAnggaran = (int) $user->active_budget_year;
        
        $items = $this->listScopedBukuAgendaSkUseCase->executeAll($level);

        $pdf = Pdf::loadView('pdf.buku_agenda_sk_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("buku-agenda-sk-{$level}-report.pdf");
    }
}
