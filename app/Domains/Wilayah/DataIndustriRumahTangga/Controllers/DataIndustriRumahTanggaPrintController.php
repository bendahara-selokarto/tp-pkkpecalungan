<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Controllers;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\UseCases\ListScopedDataIndustriRumahTanggaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class DataIndustriRumahTanggaPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedDataIndustriRumahTanggaUseCase $listScopedDataIndustriRumahTanggaUseCase,
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
        $this->authorize('viewAny', DataIndustriRumahTangga::class);

        $items = $this->listScopedDataIndustriRumahTanggaUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.data_industri_rumah_tangga_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("data-industri-rumah-tangga-{$level}-report.pdf");
    }
}




