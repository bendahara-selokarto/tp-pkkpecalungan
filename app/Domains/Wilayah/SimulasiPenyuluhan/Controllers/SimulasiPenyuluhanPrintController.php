<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\UseCases\ListScopedSimulasiPenyuluhanUseCase;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class SimulasiPenyuluhanPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedSimulasiPenyuluhanUseCase $listScopedSimulasiPenyuluhanUseCase
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
        $this->authorize('viewAny', SimulasiPenyuluhan::class);

        $items = $this->listScopedSimulasiPenyuluhanUseCase
            ->execute($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = Pdf::loadView('pdf.simulasi_penyuluhan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("simulasi-penyuluhan-{$level}-report.pdf");
    }
}
