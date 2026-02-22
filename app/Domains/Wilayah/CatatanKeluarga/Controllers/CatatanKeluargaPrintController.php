<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Controllers;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkKabupatenKotaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkKecamatanUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkDesaKelurahanUseCase;
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
        private readonly ListScopedCatatanTpPkkDesaKelurahanUseCase $listScopedCatatanTpPkkDesaKelurahanUseCase,
        private readonly ListScopedCatatanTpPkkKecamatanUseCase $listScopedCatatanTpPkkKecamatanUseCase,
        private readonly ListScopedCatatanTpPkkKabupatenKotaUseCase $listScopedCatatanTpPkkKabupatenKotaUseCase,
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

    public function printDesaCatatanTpPkkDesaKelurahanReport(): Response
    {
        return $this->streamCatatanTpPkkDesaKelurahanReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanCatatanTpPkkDesaKelurahanReport(): Response
    {
        return $this->streamCatatanTpPkkDesaKelurahanReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaCatatanTpPkkKecamatanReport(): Response
    {
        return $this->streamCatatanTpPkkKecamatanReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanCatatanTpPkkKecamatanReport(): Response
    {
        return $this->streamCatatanTpPkkKecamatanReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaCatatanTpPkkKabupatenKotaReport(): Response
    {
        return $this->streamCatatanTpPkkKabupatenKotaReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanCatatanTpPkkKabupatenKotaReport(): Response
    {
        return $this->streamCatatanTpPkkKabupatenKotaReport(ScopeLevel::KECAMATAN->value);
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

    private function streamCatatanTpPkkDesaKelurahanReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanTpPkkDesaKelurahanUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area.parent');
        $area = $user->area;
        $kecamatanName = $area?->level === ScopeLevel::DESA->value
            ? ($area->parent?->name ?? '-')
            : ($area?->name ?? '-');

        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_data_kegiatan_warga_tp_pkk_desa_kelurahan_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $area?->name ?? '-',
            'kecamatanName' => $kecamatanName,
            'kabKotaName' => '-',
            'provinsiName' => '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("catatan-data-kegiatan-warga-tp-pkk-desa-kelurahan-{$level}-report.pdf");
    }

    private function streamCatatanTpPkkKecamatanReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanTpPkkKecamatanUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area');
        $area = $user->area;

        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_data_kegiatan_warga_tp_pkk_kecamatan_report', [
            'items' => $items,
            'level' => $level,
            'kecamatanName' => $area?->name ?? '-',
            'kabKotaName' => '-',
            'provinsiName' => '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("catatan-data-kegiatan-warga-tp-pkk-kecamatan-{$level}-report.pdf");
    }

    private function streamCatatanTpPkkKabupatenKotaReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanTpPkkKabupatenKotaUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area.parent');
        $area = $user->area;
        $kecamatanName = $area?->level === ScopeLevel::DESA->value
            ? ($area->parent?->name ?? '-')
            : ($area?->name ?? '-');

        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_data_kegiatan_warga_tp_pkk_kabupaten_kota_report', [
            'items' => $items,
            'level' => $level,
            'kecamatanName' => $kecamatanName,
            'kabKotaName' => '-',
            'provinsiName' => '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("catatan-data-kegiatan-warga-tp-pkk-kabupaten-kota-{$level}-report.pdf");
    }
}
