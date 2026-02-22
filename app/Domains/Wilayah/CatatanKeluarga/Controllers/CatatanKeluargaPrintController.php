<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Controllers;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanPkkRwUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapDasaWismaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapPkkRtUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapRwUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Symfony\Component\HttpFoundation\Response;

class CatatanKeluargaPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedCatatanKeluargaUseCase $listScopedCatatanKeluargaUseCase,
        private readonly ListScopedRekapDasaWismaUseCase $listScopedRekapDasaWismaUseCase,
        private readonly ListScopedRekapPkkRtUseCase $listScopedRekapPkkRtUseCase,
        private readonly ListScopedCatatanPkkRwUseCase $listScopedCatatanPkkRwUseCase,
        private readonly ListScopedRekapRwUseCase $listScopedRekapRwUseCase,
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

    public function printDesaRekapDasaWismaReport(): Response
    {
        return $this->streamRekapDasaWismaReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapDasaWismaReport(): Response
    {
        return $this->streamRekapDasaWismaReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaRekapPkkRtReport(): Response
    {
        return $this->streamRekapPkkRtReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapPkkRtReport(): Response
    {
        return $this->streamRekapPkkRtReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaCatatanPkkRwReport(): Response
    {
        return $this->streamCatatanPkkRwReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanCatatanPkkRwReport(): Response
    {
        return $this->streamCatatanPkkRwReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaRekapRwReport(): Response
    {
        return $this->streamRekapRwReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapRwReport(): Response
    {
        return $this->streamRekapRwReport(ScopeLevel::KECAMATAN->value);
    }

    private function streamReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanKeluargaUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_keluarga_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("catatan-keluarga-{$level}-report.pdf");
    }

    private function streamRekapDasaWismaReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapDasaWismaUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_catatan_data_kegiatan_warga_dasa_wisma_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-catatan-data-kegiatan-warga-dasa-wisma-{$level}-report.pdf");
    }

    private function streamRekapPkkRtReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapPkkRtUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_catatan_data_kegiatan_warga_pkk_rt_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-catatan-data-kegiatan-warga-pkk-rt-{$level}-report.pdf");
    }

    private function streamCatatanPkkRwReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanPkkRwUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_data_kegiatan_warga_pkk_rw_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("catatan-data-kegiatan-warga-pkk-rw-{$level}-report.pdf");
    }

    private function streamRekapRwReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapRwUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_catatan_data_kegiatan_warga_rw_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-catatan-data-kegiatan-warga-rw-{$level}-report.pdf");
    }
}
