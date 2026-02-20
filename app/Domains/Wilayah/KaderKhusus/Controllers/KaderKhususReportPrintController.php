<?php

namespace App\Domains\Wilayah\KaderKhusus\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\UseCases\ListScopedKaderKhususUseCase;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class KaderKhususReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedKaderKhususUseCase $listScopedKaderKhususUseCase
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
        $this->authorize('viewAny', KaderKhusus::class);

        $items = $this->listScopedKaderKhususUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = Pdf::loadView('pdf.kader_khusus_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("kader-khusus-{$level}-report.pdf");
    }
}
