<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Controllers;

use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Repositories\AgendaSuratTugasRepository;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Http\Controllers\Controller;
use App\Support\Pdf\PdfViewFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaSuratTugasReportPrintController extends Controller
{
    public function __construct(
        private readonly AgendaSuratTugasRepository $repository,
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly PdfViewFactory $pdfViewFactory
    ) {
    }

    public function report(Request $request)
    {
        $this->authorize('print', AgendaSuratTugas::class);

        $level = $request->get('level', 'desa');
        $user = Auth::user()->loadMissing('area.parent');
        $areaId = (int) $user->area_id;
        $tahunAnggaran = (int) $user->active_budget_year;

        $items = AgendaSuratTugas::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->orderBy('tanggal_surat')
            ->orderBy('id')
            ->get();

        $area = $user->area;
        $areaName = $area?->name ?? '-';
        $pdfKecamatanName = $area?->level === ScopeLevel::DESA->value
            ? ($area->parent?->name ?? '-')
            : ($area?->name ?? '-');

        $pdf = $this->pdfViewFactory->loadView('pdf.agenda_surat_tugas_report', [
            'items' => $items,
            'level' => $level,
            'areaName' => $areaName,
            'pdfKecamatanName' => $pdfKecamatanName,
            'tahunAnggaran' => $tahunAnggaran,
            'printedBy' => $user,
            'printedAt' => now(),
        ]);

        return $pdf->stream("agenda-surat-tugas-{$level}-report.pdf");
    }
}
