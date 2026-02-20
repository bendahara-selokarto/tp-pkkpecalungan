<?php

namespace App\Domains\Wilayah\AnggotaPokja\Controllers;

use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\UseCases\ListScopedAnggotaPokjaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class AnggotaPokjaReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedAnggotaPokjaUseCase $listScopedAnggotaPokjaUseCase,
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
        $this->authorize('viewAny', AnggotaPokja::class);

        $items = $this->listScopedAnggotaPokjaUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.anggota_pokja_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("anggota-pokja-{$level}-report.pdf");
    }
}
