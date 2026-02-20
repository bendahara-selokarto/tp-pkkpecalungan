<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases\ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class DataPemanfaatanTanahPekaranganHatinyaPkkPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase $listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase,
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
        $this->authorize('viewAny', DataPemanfaatanTanahPekaranganHatinyaPkk::class);

        $items = $this->listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.data_pemanfaatan_tanah_pekarangan_hatinya_pkk_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("data-pemanfaatan-tanah-pekarangan-hatinya-pkk-{$level}-report.pdf");
    }
}




