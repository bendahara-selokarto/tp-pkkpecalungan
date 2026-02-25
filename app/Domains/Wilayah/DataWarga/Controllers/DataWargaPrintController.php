<?php

namespace App\Domains\Wilayah\DataWarga\Controllers;

use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\UseCases\ListScopedDataWargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class DataWargaPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedDataWargaUseCase $listScopedDataWargaUseCase,
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
        $this->authorize('viewAny', DataWarga::class);

        $items = $this->listScopedDataWargaUseCase
            ->executeAll($level)
            ->load('anggota')
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.data_warga_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ], PdfViewFactory::ORIENTATION_PORTRAIT);

        return $pdf->stream("data-warga-{$level}-report.pdf");
    }
}
