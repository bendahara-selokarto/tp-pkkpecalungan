<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions\CreatePilotProjectNaskahPelaporanAction;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions\DeletePilotProjectNaskahPelaporanAction;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Actions\UpdatePilotProjectNaskahPelaporanAction;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanAttachment;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models\PilotProjectNaskahPelaporanReport;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Requests\ListPilotProjectNaskahPelaporanRequest;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Requests\StorePilotProjectNaskahPelaporanRequest;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\Requests\UpdatePilotProjectNaskahPelaporanRequest;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases\GetScopedPilotProjectNaskahPelaporanUseCase;
use App\Domains\Wilayah\PilotProjectNaskahPelaporan\UseCases\ListScopedPilotProjectNaskahPelaporanUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanPilotProjectNaskahPelaporanController extends Controller
{
    public function __construct(
        private readonly ListScopedPilotProjectNaskahPelaporanUseCase $listUseCase,
        private readonly GetScopedPilotProjectNaskahPelaporanUseCase $getUseCase,
        private readonly CreatePilotProjectNaskahPelaporanAction $createAction,
        private readonly UpdatePilotProjectNaskahPelaporanAction $updateAction,
        private readonly DeletePilotProjectNaskahPelaporanAction $deleteAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListPilotProjectNaskahPelaporanRequest $request): Response
    {
        $this->authorize('viewAny', PilotProjectNaskahPelaporanReport::class);

        $reports = $this->listUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (PilotProjectNaskahPelaporanReport $report) => [
                'id' => $report->id,
                'judul_laporan' => $report->judul_laporan,
                'attachments_count' => $report->attachments_count ?? 0,
                'updated_at' => $report->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('PilotProjectNaskahPelaporan/Index', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/pilot-project-naskah-pelaporan',
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
        $this->authorize('create', PilotProjectNaskahPelaporanReport::class);

        return Inertia::render('PilotProjectNaskahPelaporan/Create', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/pilot-project-naskah-pelaporan',
            'defaultTitle' => config('pilot_project_naskah_pelaporan.module.label'),
            'defaultLetterFrom' => $this->defaultLetterFrom('Kecamatan'),
            'attachmentCategories' => config('pilot_project_naskah_pelaporan.attachment_categories', []),
        ]);
    }

    public function store(StorePilotProjectNaskahPelaporanRequest $request): RedirectResponse
    {
        $this->authorize('create', PilotProjectNaskahPelaporanReport::class);
        $this->createAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()
            ->route('kecamatan.pilot-project-naskah-pelaporan.index')
            ->with('success', 'Naskah pelaporan pilot project berhasil dibuat.');
    }

    public function show(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $report);

        return Inertia::render('PilotProjectNaskahPelaporan/Show', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/pilot-project-naskah-pelaporan',
            'attachmentCategories' => config('pilot_project_naskah_pelaporan.attachment_categories', []),
            'report' => $this->serializeReport($report),
        ]);
    }

    public function edit(int $id): Response
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $report);

        return Inertia::render('PilotProjectNaskahPelaporan/Edit', [
            'scopeLabel' => 'Kecamatan',
            'scopePrefix' => '/kecamatan/pilot-project-naskah-pelaporan',
            'defaultLetterFrom' => $this->defaultLetterFrom('Kecamatan'),
            'attachmentCategories' => config('pilot_project_naskah_pelaporan.attachment_categories', []),
            'report' => $this->serializeReport($report),
        ]);
    }

    public function update(UpdatePilotProjectNaskahPelaporanRequest $request, int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $report);
        $this->updateAction->execute($report, $request->validated());

        return redirect()
            ->route('kecamatan.pilot-project-naskah-pelaporan.index')
            ->with('success', 'Naskah pelaporan pilot project berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $report = $this->getUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $report);
        $this->deleteAction->execute($report);

        return redirect()
            ->route('kecamatan.pilot-project-naskah-pelaporan.index')
            ->with('success', 'Naskah pelaporan pilot project berhasil dihapus.');
    }

    private function serializeReport(PilotProjectNaskahPelaporanReport $report): array
    {
        return [
            'id' => $report->id,
            'judul_laporan' => $report->judul_laporan,
            'surat_kepada' => $report->surat_kepada,
            'surat_dari' => $report->surat_dari,
            'surat_tembusan' => $report->surat_tembusan,
            'surat_tanggal' => $report->surat_tanggal?->toDateString(),
            'surat_nomor' => $report->surat_nomor,
            'surat_sifat' => $report->surat_sifat,
            'surat_lampiran' => $report->surat_lampiran,
            'surat_hal' => $report->surat_hal,
            'dasar_pelaksanaan' => $report->dasar_pelaksanaan,
            'pendahuluan' => $report->pendahuluan,
            'pelaksanaan_1' => $report->pelaksanaan_1,
            'pelaksanaan_2' => $report->pelaksanaan_2,
            'pelaksanaan_3' => $report->pelaksanaan_3,
            'pelaksanaan_4' => $report->pelaksanaan_4,
            'pelaksanaan_5' => $report->pelaksanaan_5,
            'penutup' => $report->penutup,
            'attachments' => $report->attachments->map(function (PilotProjectNaskahPelaporanAttachment $attachment): array {
                return [
                    'id' => $attachment->id,
                    'category' => $attachment->category,
                    'original_name' => $attachment->original_name,
                    'mime_type' => $attachment->mime_type,
                    'file_size' => $attachment->file_size,
                    'file_url' => Storage::disk('public')->url($attachment->file_path),
                ];
            })->values(),
        ];
    }

    private function defaultLetterFrom(string $scopeTitle): string
    {
        $user = auth()->user()?->loadMissing('area');
        $areaName = trim((string) ($user?->area?->name ?? ''));

        return trim(sprintf('Tim Penggerak PKK %s %s', $scopeTitle, $areaName));
    }
}

