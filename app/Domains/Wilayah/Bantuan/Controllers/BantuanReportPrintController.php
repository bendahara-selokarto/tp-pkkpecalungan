<?php

namespace App\Domains\Wilayah\Bantuan\Controllers;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Bantuan\UseCases\ListScopedBantuanUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class BantuanReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedBantuanUseCase $listScopedBantuanUseCase
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
        $this->authorize('viewAny', Bantuan::class);

        $items = $this->listScopedBantuanUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = Pdf::loadView('pdf.bantuan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("bantuan-{$level}-report.pdf");
    }
}
