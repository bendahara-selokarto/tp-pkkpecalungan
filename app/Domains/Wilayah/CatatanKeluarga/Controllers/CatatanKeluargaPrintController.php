<?php

namespace App\Domains\Wilayah\CatatanKeluarga\Controllers;

use App\Domains\Wilayah\CatatanKeluarga\Models\CatatanKeluarga;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanKeluargaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkKabupatenKotaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkKecamatanUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkDesaKelurahanUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanTpPkkProvinsiUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedCatatanPkkRwUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapDasaWismaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapIbuHamilDasaWismaUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapIbuHamilPkkRtUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapIbuHamilPkkRwUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapPkkRtUseCase;
use App\Domains\Wilayah\CatatanKeluarga\UseCases\ListScopedRekapRwUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class CatatanKeluargaPrintController extends Controller
{
    public function __construct(
        private readonly ListScopedCatatanKeluargaUseCase $listScopedCatatanKeluargaUseCase,
        private readonly ListScopedCatatanTpPkkDesaKelurahanUseCase $listScopedCatatanTpPkkDesaKelurahanUseCase,
        private readonly ListScopedCatatanTpPkkKecamatanUseCase $listScopedCatatanTpPkkKecamatanUseCase,
        private readonly ListScopedCatatanTpPkkKabupatenKotaUseCase $listScopedCatatanTpPkkKabupatenKotaUseCase,
        private readonly ListScopedCatatanTpPkkProvinsiUseCase $listScopedCatatanTpPkkProvinsiUseCase,
        private readonly ListScopedRekapDasaWismaUseCase $listScopedRekapDasaWismaUseCase,
        private readonly ListScopedRekapIbuHamilDasaWismaUseCase $listScopedRekapIbuHamilDasaWismaUseCase,
        private readonly ListScopedRekapIbuHamilPkkRtUseCase $listScopedRekapIbuHamilPkkRtUseCase,
        private readonly ListScopedRekapIbuHamilPkkRwUseCase $listScopedRekapIbuHamilPkkRwUseCase,
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

    public function printDesaRekapIbuHamilDasaWismaReport(): Response
    {
        return $this->streamRekapIbuHamilDasaWismaReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapIbuHamilDasaWismaReport(): Response
    {
        return $this->streamRekapIbuHamilDasaWismaReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaRekapIbuHamilPkkRtReport(): Response
    {
        return $this->streamRekapIbuHamilPkkRtReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapIbuHamilPkkRtReport(): Response
    {
        return $this->streamRekapIbuHamilPkkRtReport(ScopeLevel::KECAMATAN->value);
    }

    public function printDesaRekapIbuHamilPkkRwReport(): Response
    {
        return $this->streamRekapIbuHamilPkkRwReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanRekapIbuHamilPkkRwReport(): Response
    {
        return $this->streamRekapIbuHamilPkkRwReport(ScopeLevel::KECAMATAN->value);
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

    public function printDesaCatatanTpPkkProvinsiReport(): Response
    {
        return $this->streamCatatanTpPkkProvinsiReport(ScopeLevel::DESA->value);
    }

    public function printKecamatanCatatanTpPkkProvinsiReport(): Response
    {
        return $this->streamCatatanTpPkkProvinsiReport(ScopeLevel::KECAMATAN->value);
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

    private function streamRekapIbuHamilDasaWismaReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapIbuHamilDasaWismaUseCase
            ->execute($level)
            ->values();

        $notes = [
            'jumlah_ibu_hamil' => $items->where('status_ibu', 'HAMIL')->count(),
            'jumlah_ibu_melahirkan' => $items->where('status_ibu', 'MELAHIRKAN')->count(),
            'jumlah_ibu_nifas' => $items->where('status_ibu', 'NIFAS')->count(),
            'jumlah_ibu_meninggal' => $items->where('catatan_kematian_status', 'IBU')->count(),
            'jumlah_bayi_lahir' => $items->filter(fn (array $item): bool => ($item['nama_bayi'] ?? '-') !== '-')->count(),
            'jumlah_bayi_meninggal' => $items->where('catatan_kematian_status', 'BAYI')->count(),
            'jumlah_kematian_balita' => $items->where('catatan_kematian_status', 'BALITA')->count(),
            'jumlah_ibu_meninggal_risiko' => $items->where('catatan_kematian_status', 'IBU')->count(),
        ];

        $totals = [
            'kelahiran_l' => (int) $items->sum('kelahiran_l'),
            'kelahiran_p' => (int) $items->sum('kelahiran_p'),
            'akta_ada' => (int) $items->sum('akta_ada'),
            'akta_tidak_ada' => (int) $items->sum('akta_tidak_ada'),
            'kematian_l' => (int) $items->sum('kematian_l'),
            'kematian_p' => (int) $items->sum('kematian_p'),
        ];

        $meta = [
            'kelompok_dasawisma' => $this->composeMetaLabel($items->pluck('kelompok_dasawisma')),
            'kelompok_pkk_rt' => $this->composeMetaLabel($items->pluck('kelompok_pkk_rt')),
            'kelompok_pkk_rw' => $this->composeMetaLabel($items->pluck('kelompok_pkk_rw')),
            'dusun_lingkungan' => $this->composeMetaLabel($items->pluck('dusun_lingkungan')),
            'desa_kelurahan' => $this->composeMetaLabel($items->pluck('desa_kelurahan')),
        ];

        $user = auth()->user()->loadMissing('area');
        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_ibu_hamil_melahirkan_dasawisma_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $user->area?->name ?? '-',
            'notes' => $notes,
            'totals' => $totals,
            'meta' => $meta,
            'printedBy' => $user,
            'printedAt' => now(),
            'bulan' => now()->translatedFormat('F'),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-ibu-hamil-melahirkan-dasawisma-{$level}-report.pdf");
    }

    private function streamRekapIbuHamilPkkRtReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapIbuHamilPkkRtUseCase
            ->execute($level)
            ->values();

        $totals = [
            'jumlah_ibu_hamil' => (int) $items->sum('jumlah_ibu_hamil'),
            'jumlah_ibu_melahirkan' => (int) $items->sum('jumlah_ibu_melahirkan'),
            'jumlah_ibu_nifas' => (int) $items->sum('jumlah_ibu_nifas'),
            'jumlah_ibu_meninggal' => (int) $items->sum('jumlah_ibu_meninggal'),
            'jumlah_bayi_lahir_l' => (int) $items->sum('jumlah_bayi_lahir_l'),
            'jumlah_bayi_lahir_p' => (int) $items->sum('jumlah_bayi_lahir_p'),
            'jumlah_akte_kelahiran_ada' => (int) $items->sum('jumlah_akte_kelahiran_ada'),
            'jumlah_akte_kelahiran_tidak_ada' => (int) $items->sum('jumlah_akte_kelahiran_tidak_ada'),
            'jumlah_bayi_meninggal_l' => (int) $items->sum('jumlah_bayi_meninggal_l'),
            'jumlah_bayi_meninggal_p' => (int) $items->sum('jumlah_bayi_meninggal_p'),
            'jumlah_balita_meninggal_l' => (int) $items->sum('jumlah_balita_meninggal_l'),
            'jumlah_balita_meninggal_p' => (int) $items->sum('jumlah_balita_meninggal_p'),
        ];

        $user = auth()->user()->loadMissing('area.parent');
        $area = $user->area;

        $meta = [
            'rt_rw_dus_ling' => $this->composeMetaLabel($items->pluck('rt_rw_dus_ling')),
            'desa_kelurahan' => $this->composeMetaLabel($items->pluck('desa_kelurahan')),
            'kecamatan' => '-',
            'kab_kota' => '-',
            'provinsi' => '-',
        ];

        if ($area?->level === ScopeLevel::DESA->value) {
            $meta['desa_kelurahan'] = trim((string) ($area->name ?? '')) !== '' ? trim((string) $area->name) : $meta['desa_kelurahan'];
            $meta['kecamatan'] = trim((string) ($area->parent?->name ?? '')) !== '' ? trim((string) $area->parent?->name) : '-';
        }

        if ($area?->level === ScopeLevel::KECAMATAN->value) {
            $meta['kecamatan'] = trim((string) ($area->name ?? '')) !== '' ? trim((string) $area->name) : '-';
            if ($meta['desa_kelurahan'] === '-') {
                $meta['desa_kelurahan'] = 'MULTI';
            }
        }

        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_ibu_hamil_melahirkan_pkk_rt_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $area?->name ?? '-',
            'totals' => $totals,
            'meta' => $meta,
            'printedBy' => $user,
            'printedAt' => now(),
            'bulan' => now()->translatedFormat('F'),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-ibu-hamil-melahirkan-pkk-rt-{$level}-report.pdf");
    }

    private function streamRekapIbuHamilPkkRwReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedRekapIbuHamilPkkRwUseCase
            ->execute($level)
            ->values();

        $totals = [
            'jumlah_kelompok_dasawisma' => (int) $items->sum('jumlah_kelompok_dasawisma'),
            'jumlah_ibu_hamil' => (int) $items->sum('jumlah_ibu_hamil'),
            'jumlah_ibu_melahirkan' => (int) $items->sum('jumlah_ibu_melahirkan'),
            'jumlah_ibu_nifas' => (int) $items->sum('jumlah_ibu_nifas'),
            'jumlah_ibu_meninggal' => (int) $items->sum('jumlah_ibu_meninggal'),
            'jumlah_bayi_lahir_l' => (int) $items->sum('jumlah_bayi_lahir_l'),
            'jumlah_bayi_lahir_p' => (int) $items->sum('jumlah_bayi_lahir_p'),
            'jumlah_akte_kelahiran_ada' => (int) $items->sum('jumlah_akte_kelahiran_ada'),
            'jumlah_akte_kelahiran_tidak_ada' => (int) $items->sum('jumlah_akte_kelahiran_tidak_ada'),
            'jumlah_bayi_meninggal_l' => (int) $items->sum('jumlah_bayi_meninggal_l'),
            'jumlah_bayi_meninggal_p' => (int) $items->sum('jumlah_bayi_meninggal_p'),
            'jumlah_balita_meninggal_l' => (int) $items->sum('jumlah_balita_meninggal_l'),
            'jumlah_balita_meninggal_p' => (int) $items->sum('jumlah_balita_meninggal_p'),
        ];

        $user = auth()->user()->loadMissing('area.parent');
        $area = $user->area;

        $meta = [
            'rw' => $this->composeMetaLabel($items->pluck('nomor_rw')),
            'dusun_lingkungan' => $this->composeMetaLabel($items->pluck('dusun_lingkungan')),
            'desa_kelurahan' => $this->composeMetaLabel($items->pluck('desa_kelurahan')),
            'kecamatan' => '-',
            'kab_kota' => '-',
            'provinsi' => '-',
        ];

        if ($area?->level === ScopeLevel::DESA->value) {
            $meta['desa_kelurahan'] = trim((string) ($area->name ?? '')) !== '' ? trim((string) $area->name) : $meta['desa_kelurahan'];
            $meta['kecamatan'] = trim((string) ($area->parent?->name ?? '')) !== '' ? trim((string) $area->parent?->name) : '-';
        }

        if ($area?->level === ScopeLevel::KECAMATAN->value) {
            $meta['kecamatan'] = trim((string) ($area->name ?? '')) !== '' ? trim((string) $area->name) : '-';
            if ($meta['desa_kelurahan'] === '-') {
                $meta['desa_kelurahan'] = 'MULTI';
            }
            if ($meta['rw'] === '-') {
                $meta['rw'] = 'MULTI';
            }
        }

        $pdf = $this->pdfViewFactory->loadView('pdf.rekap_ibu_hamil_melahirkan_pkk_rw_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $area?->name ?? '-',
            'totals' => $totals,
            'meta' => $meta,
            'printedBy' => $user,
            'printedAt' => now(),
            'bulan' => now()->translatedFormat('F'),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("rekap-ibu-hamil-melahirkan-pkk-rw-{$level}-report.pdf");
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

    private function streamCatatanTpPkkProvinsiReport(string $level): Response
    {
        $this->authorize('viewAny', CatatanKeluarga::class);

        $items = $this->listScopedCatatanTpPkkProvinsiUseCase
            ->execute($level)
            ->values();

        $user = auth()->user()->loadMissing('area.parent');
        $area = $user->area;
        $kecamatanName = $area?->level === ScopeLevel::DESA->value
            ? ($area->parent?->name ?? '-')
            : ($area?->name ?? '-');

        $pdf = $this->pdfViewFactory->loadView('pdf.catatan_data_kegiatan_warga_tp_pkk_provinsi_report', [
            'items' => $items,
            'level' => $level,
            'provinsiName' => '-',
            'kecamatanName' => $kecamatanName,
            'kabKotaName' => '-',
            'printedBy' => $user,
            'printedAt' => now(),
            'tahun' => now()->format('Y'),
        ]);

        return $pdf->stream("catatan-data-kegiatan-warga-tp-pkk-provinsi-{$level}-report.pdf");
    }

    /**
     * @param Collection<int, mixed> $values
     */
    private function composeMetaLabel(Collection $values): string
    {
        $normalized = $values
            ->map(fn ($value): string => trim((string) $value))
            ->filter(fn (string $value): bool => $value !== '' && $value !== '-')
            ->unique()
            ->values();

        if ($normalized->isEmpty()) {
            return '-';
        }

        if ($normalized->count() === 1) {
            return (string) $normalized->first();
        }

        return 'MULTI: '.$normalized->implode(', ');
    }
}
