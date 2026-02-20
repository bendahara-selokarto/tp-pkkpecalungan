<?php

namespace App\Domains\Wilayah\Bantuan\Controllers;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\UseCases\ListScopedBantuanUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class BantuanReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBantuanUseCase $listScopedBantuanUseCase,
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

    public function printDesaKeuanganReport(): Response
    {
        return $this->streamKeuanganReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanKeuanganReport(): Response
    {
        return $this->streamKeuanganReport(ScopeLevel::KECAMATAN->value);
    }

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', Bantuan::class);

        $items = $this->listScopedBantuanUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.bantuan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("bantuan-{$level}-report.pdf");
    }

    private function streamKeuanganReport(string $level): Response
    {
        $this->authorize('viewAny', Bantuan::class);

        $items = $this->listScopedBantuanUseCase
            ->execute($level)
            ->filter(fn (Bantuan $item): bool => $this->isKeuanganCategory((string) $item->category))
            ->sortBy(fn (Bantuan $item): string => sprintf('%s-%010d', $item->received_date, $item->id))
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.buku_keuangan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("buku-keuangan-{$level}-report.pdf");
    }

    private function isKeuanganCategory(string $category): bool
    {
        $normalizedCategory = strtolower($category);

        return str_contains($normalizedCategory, 'keuangan')
            || str_contains($normalizedCategory, 'uang');
    }
}
