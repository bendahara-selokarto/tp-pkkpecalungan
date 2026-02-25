<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Controllers;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\UseCases\ListScopedAnggotaDanKaderUseCase;
use App\Domains\Wilayah\AnggotaTimPenggerak\UseCases\ListScopedAnggotaTimPenggerakUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class AnggotaTimPenggerakReportPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedAnggotaTimPenggerakUseCase $listScopedAnggotaTimPenggerakUseCase,
        private readonly ListScopedAnggotaDanKaderUseCase $listScopedAnggotaDanKaderUseCase,
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

    public function printDesaAnggotaDanKaderReport(): Response
    {
        return $this->streamAnggotaDanKaderReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanAnggotaDanKaderReport(): Response
    {
        return $this->streamAnggotaDanKaderReport(ScopeLevel::KECAMATAN->value);
    }

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', AnggotaTimPenggerak::class);

        $items = $this->listScopedAnggotaTimPenggerakUseCase
            ->executeAll($level)
            ->sortBy('id')
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.anggota_tim_penggerak_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("anggota-tim-penggerak-{$level}-report.pdf");
    }

    private function streamAnggotaDanKaderReport(string $level): Response
    {
        $this->authorize('viewAny', AnggotaTimPenggerak::class);
        $this->authorize('viewAny', KaderKhusus::class);

        $items = $this->listScopedAnggotaDanKaderUseCase->execute($level);

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.anggota_dan_kader_report', [
            'anggotaTimPenggeraks' => $items['anggotaTimPenggerak'],
            'kaderKhusus' => $items['kaderKhusus'],
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("anggota-dan-kader-{$level}-report.pdf");
    }
}
