<?php

namespace App\Domains\Wilayah\BukuKeuangan\Controllers;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use App\Domains\Wilayah\BukuKeuangan\UseCases\ListScopedBukuKeuanganUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class BukuKeuanganReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuKeuanganUseCase $listScopedBukuKeuanganUseCase,
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
        $this->authorize('viewAny', BukuKeuangan::class);

        $items = $this->listScopedBukuKeuanganUseCase
            ->execute($level)
            ->sortBy(fn (BukuKeuangan $item): string => sprintf(
                '%s-%010d',
                $item->transaction_date?->format('Y-m-d'),
                $item->id
            ))
            ->values();

        $entries = $items->map(function (BukuKeuangan $item): array {
            $amount = (float) $item->amount;
            $isPengeluaran = $item->entry_type === BukuKeuangan::ENTRY_TYPE_PENGELUARAN;
            $nominal = abs($amount);

            return [
                'tanggal' => $item->transaction_date?->format('Y-m-d'),
                'uraian' => $item->description,
                'sumber' => $item->source,
                'nomor_bukti_kas' => $item->reference_number,
                'pemasukan' => $isPengeluaran ? 0.0 : $nominal,
                'pengeluaran' => $isPengeluaran ? $nominal : 0.0,
            ];
        });

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.buku_keuangan_report', [
            'entries' => $entries,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("buku-keuangan-{$level}-report.pdf");
    }
}
