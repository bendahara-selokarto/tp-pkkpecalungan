<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\LaporanTahunanPkk\Actions\CreateLaporanTahunanPkkAction;
use App\Domains\Wilayah\LaporanTahunanPkk\Actions\DeleteLaporanTahunanPkkAction;
use App\Domains\Wilayah\LaporanTahunanPkk\Actions\UpdateLaporanTahunanPkkAction;
use App\Domains\Wilayah\LaporanTahunanPkk\Models\LaporanTahunanPkkReport;
use App\Domains\Wilayah\LaporanTahunanPkk\Requests\ListLaporanTahunanPkkRequest;
use App\Domains\Wilayah\LaporanTahunanPkk\Requests\StoreLaporanTahunanPkkRequest;
use App\Domains\Wilayah\LaporanTahunanPkk\Requests\UpdateLaporanTahunanPkkRequest;
use App\Domains\Wilayah\LaporanTahunanPkk\UseCases\BuildLaporanTahunanPkkDocumentUseCase;
use App\Domains\Wilayah\LaporanTahunanPkk\UseCases\GetScopedLaporanTahunanPkkUseCase;
use App\Domains\Wilayah\LaporanTahunanPkk\UseCases\ListScopedLaporanTahunanPkkUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanLaporanTahunanPkkController extends Controller
{
    public function __construct(
        private readonly ListScopedLaporanTahunanPkkUseCase $listUseCase,
        private readonly GetScopedLaporanTahunanPkkUseCase $getUseCase,
        private readonly BuildLaporanTahunanPkkDocumentUseCase $buildDocumentUseCase,
        private readonly CreateLaporanTahunanPkkAction $createAction,
        private readonly UpdateLaporanTahunanPkkAction $updateAction,
        private readonly DeleteLaporanTahunanPkkAction $deleteAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListLaporanTahunanPkkRequest $request): Response
    {
        $this->authorize('viewAny', LaporanTahunanPkkReport::class);

        $reports = $this->listUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (LaporanTahunanPkkReport $report) => [
                'id' => $report->id,
                'judul_laporan' => $report->judul_laporan,
                'tahun_laporan' => $report->tahun_laporan,
                'entries_count' => $report->entries_count ?? 0,
                'updated_at' => $report->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('LaporanTahunanPkk/Index', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/laporan-tahunan-pkk',
            'reports' => $reports,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', LaporanTahunanPkkReport::class);

        return Inertia::render('LaporanTahunanPkk/Create', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/laporan-tahunan-pkk',
            'defaultTitle' => config('laporan_tahunan_pkk.module.label'),
            'defaultYear' => now()->year,
            'defaultCompiledBy' => $this->defaultCompiledBy('Kecamatan'),
            'defaultSignerRole' => $this->defaultSignerRole('Kecamatan'),
            'bidangOptions' => config('laporan_tahunan_pkk.bidang_options', []),
            'bidangLabels' => config('laporan_tahunan_pkk.bidang_labels', []),
        ]);
    }

    public function store(StoreLaporanTahunanPkkRequest $request): RedirectResponse
    {
        $this->authorize('create', LaporanTahunanPkkReport::class);
        $this->createAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()
            ->route('kecamatan.laporan-tahunan-pkk.index')
            ->with('success', 'Laporan tahunan TP PKK berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $report);

        $documentData = $this->buildDocumentUseCase->execute($report);

        return Inertia::render('LaporanTahunanPkk/Show', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/laporan-tahunan-pkk',
            'bidangLabels' => $documentData['bidang_labels'],
            'groupedEntries' => $documentData['grouped_entries'],
            'report' => $this->serializeReport($report),
        ]);
    }

    public function edit(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $report);

        return Inertia::render('LaporanTahunanPkk/Edit', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/laporan-tahunan-pkk',
            'defaultCompiledBy' => $this->defaultCompiledBy('Kecamatan'),
            'defaultSignerRole' => $this->defaultSignerRole('Kecamatan'),
            'bidangOptions' => config('laporan_tahunan_pkk.bidang_options', []),
            'bidangLabels' => config('laporan_tahunan_pkk.bidang_labels', []),
            'report' => $this->serializeReport($report),
        ]);
    }

    public function update(UpdateLaporanTahunanPkkRequest $request, int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $report);
        $this->updateAction->execute($report, $request->validated());

        return redirect()
            ->route('kecamatan.laporan-tahunan-pkk.index')
            ->with('success', 'Laporan tahunan TP PKK berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $report);
        $this->deleteAction->execute($report);

        return redirect()
            ->route('kecamatan.laporan-tahunan-pkk.index')
            ->with('success', 'Laporan tahunan TP PKK berhasil dihapus.');
    }

    private function serializeReport(LaporanTahunanPkkReport $report): array
    {
        return [
            'id' => $report->id,
            'judul_laporan' => $report->judul_laporan,
            'tahun_laporan' => $report->tahun_laporan,
            'pendahuluan' => $report->pendahuluan,
            'keberhasilan' => $report->keberhasilan,
            'hambatan' => $report->hambatan,
            'kesimpulan' => $report->kesimpulan,
            'penutup' => $report->penutup,
            'disusun_oleh' => $report->disusun_oleh,
            'jabatan_penanda_tangan' => $report->jabatan_penanda_tangan,
            'nama_penanda_tangan' => $report->nama_penanda_tangan,
            'manual_entries' => $report->entries
                ->map(fn ($entry) => [
                    'id' => $entry->id,
                    'bidang' => $entry->bidang,
                    'activity_date' => $entry->activity_date?->toDateString(),
                    'description' => $entry->description,
                ])
                ->values(),
        ];
    }

    private function defaultCompiledBy(string $scopeTitle): string
    {
        $user = auth()->user()?->loadMissing('area');
        $areaName = trim((string) ($user?->area?->name ?? ''));

        return trim(sprintf('Tim Penggerak PKK %s %s', $scopeTitle, $areaName));
    }

    private function defaultSignerRole(string $scopeTitle): string
    {
        return sprintf('Ketua TP. PKK %s', strtoupper($scopeTitle));
    }
}
