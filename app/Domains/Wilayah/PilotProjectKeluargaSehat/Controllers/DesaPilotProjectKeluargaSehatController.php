<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions\CreatePilotProjectKeluargaSehatAction;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions\DeletePilotProjectKeluargaSehatAction;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Actions\UpdatePilotProjectKeluargaSehatAction;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Models\PilotProjectKeluargaSehatReport;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Requests\StorePilotProjectKeluargaSehatRequest;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\Requests\UpdatePilotProjectKeluargaSehatRequest;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases\GetScopedPilotProjectKeluargaSehatUseCase;
use App\Domains\Wilayah\PilotProjectKeluargaSehat\UseCases\ListScopedPilotProjectKeluargaSehatUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaPilotProjectKeluargaSehatController extends Controller
{
    public function __construct(
        private readonly ListScopedPilotProjectKeluargaSehatUseCase $listUseCase,
        private readonly GetScopedPilotProjectKeluargaSehatUseCase $getUseCase,
        private readonly CreatePilotProjectKeluargaSehatAction $createAction,
        private readonly UpdatePilotProjectKeluargaSehatAction $updateAction,
        private readonly DeletePilotProjectKeluargaSehatAction $deleteAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', PilotProjectKeluargaSehatReport::class);

        $reports = $this->listUseCase->execute(ScopeLevel::DESA->value)
            ->map(fn (PilotProjectKeluargaSehatReport $report) => [
                'id' => $report->id,
                'judul_laporan' => $report->judul_laporan,
                'tahun_awal' => $report->tahun_awal,
                'tahun_akhir' => $report->tahun_akhir,
                'values_count' => $report->values_count ?? 0,
                'updated_at' => $report->updated_at?->toDateTimeString(),
            ])
            ->values();

        return Inertia::render('PilotProjectKeluargaSehat/Index', [
            'scopeLabel' => 'Desa',
            'scopePrefix' => '/desa/pilot-project-keluarga-sehat',
            'reports' => $reports,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', PilotProjectKeluargaSehatReport::class);

        return Inertia::render('PilotProjectKeluargaSehat/Create', [
            'scopeLabel' => 'Desa',
            'scopePrefix' => '/desa/pilot-project-keluarga-sehat',
            'sections' => config('pilot_project_keluarga_sehat.sections', []),
            'defaultPeriod' => config('pilot_project_keluarga_sehat.module.default_period', []),
            'defaultTitle' => config('pilot_project_keluarga_sehat.module.label'),
        ]);
    }

    public function store(StorePilotProjectKeluargaSehatRequest $request): RedirectResponse
    {
        $this->authorize('create', PilotProjectKeluargaSehatReport::class);
        $this->createAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()
            ->route('desa.pilot-project-keluarga-sehat.index')
            ->with('success', 'Laporan pilot project berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $report);

        return Inertia::render('PilotProjectKeluargaSehat/Show', [
            'scopeLabel' => 'Desa',
            'scopePrefix' => '/desa/pilot-project-keluarga-sehat',
            'report' => $this->serializeReport($report),
        ]);
    }

    public function edit(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $report);

        return Inertia::render('PilotProjectKeluargaSehat/Edit', [
            'scopeLabel' => 'Desa',
            'scopePrefix' => '/desa/pilot-project-keluarga-sehat',
            'sections' => config('pilot_project_keluarga_sehat.sections', []),
            'report' => $this->serializeReport($report),
        ]);
    }

    public function update(UpdatePilotProjectKeluargaSehatRequest $request, int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $report);
        $this->updateAction->execute($report, $request->validated());

        return redirect()
            ->route('desa.pilot-project-keluarga-sehat.index')
            ->with('success', 'Laporan pilot project berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $report);
        $this->deleteAction->execute($report);

        return redirect()
            ->route('desa.pilot-project-keluarga-sehat.index')
            ->with('success', 'Laporan pilot project berhasil dihapus.');
    }

    private function serializeReport(PilotProjectKeluargaSehatReport $report): array
    {
        return [
            'id' => $report->id,
            'judul_laporan' => $report->judul_laporan,
            'dasar_hukum' => $report->dasar_hukum,
            'pendahuluan' => $report->pendahuluan,
            'maksud_tujuan' => $report->maksud_tujuan,
            'pelaksanaan' => $report->pelaksanaan,
            'dokumentasi' => $report->dokumentasi,
            'penutup' => $report->penutup,
            'tahun_awal' => $report->tahun_awal,
            'tahun_akhir' => $report->tahun_akhir,
            'values' => $report->values
                ->sortBy('sort_order')
                ->values()
                ->map(fn ($value) => [
                    'id' => $value->id,
                    'section' => $value->section,
                    'cluster_code' => $value->cluster_code,
                    'indicator_code' => $value->indicator_code,
                    'indicator_label' => $value->indicator_label,
                    'year' => $value->year,
                    'semester' => $value->semester,
                    'value' => $value->value,
                    'evaluation_note' => $value->evaluation_note,
                    'keterangan_note' => $value->keterangan_note,
                    'sort_order' => $value->sort_order,
                ]),
        ];
    }
}
