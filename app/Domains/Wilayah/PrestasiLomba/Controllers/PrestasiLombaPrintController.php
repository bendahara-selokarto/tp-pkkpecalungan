<?php

namespace App\Domains\Wilayah\PrestasiLomba\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\UseCases\ListScopedPrestasiLombaUseCase;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class PrestasiLombaPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedPrestasiLombaUseCase $listScopedPrestasiLombaUseCase,
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
        $this->authorize('viewAny', PrestasiLomba::class);

        $items = $this->listScopedPrestasiLombaUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.prestasi_lomba_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("prestasi-lomba-{$level}-report.pdf");
    }
}
