<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Controllers;

use App\Domains\Wilayah\AgendaSuratTugas\Actions\CreateScopedAgendaSuratTugasAction;
use App\Domains\Wilayah\AgendaSuratTugas\Actions\DeleteAgendaSuratTugasAction;
use App\Domains\Wilayah\AgendaSuratTugas\Actions\UpdateAgendaSuratTugasAction;
use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Domains\Wilayah\AgendaSuratTugas\Requests\ListAgendaSuratTugasRequest;
use App\Domains\Wilayah\AgendaSuratTugas\Requests\StoreAgendaSuratTugasRequest;
use App\Domains\Wilayah\AgendaSuratTugas\Requests\UpdateAgendaSuratTugasRequest;
use App\Domains\Wilayah\AgendaSuratTugas\UseCases\GetScopedAgendaSuratTugasUseCase;
use App\Domains\Wilayah\AgendaSuratTugas\UseCases\ListScopedAgendaSuratTugasUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanAgendaSuratTugasController extends Controller
{
    public function __construct(
        private readonly ListScopedAgendaSuratTugasUseCase $listUseCase,
        private readonly GetScopedAgendaSuratTugasUseCase $getUseCase,
        private readonly CreateScopedAgendaSuratTugasAction $createAction,
        private readonly UpdateAgendaSuratTugasAction $updateAction,
        private readonly DeleteAgendaSuratTugasAction $deleteAction
    ) {
    }

    public function index(ListAgendaSuratTugasRequest $request): Response
    {
        $this->authorize('viewAny', AgendaSuratTugas::class);
        $items = $this->listUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (AgendaSuratTugas $item) => [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat,
                'tanggal_surat' => $this->formatDateForPayload($item->tanggal_surat),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'lampiran' => $item->lampiran,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_url,
            ]);

        return Inertia::render('Kecamatan/AgendaSuratTugas/Index', [
            'items' => $items,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'tahun_anggaran' => (int) $request->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', AgendaSuratTugas::class);

        return Inertia::render('Kecamatan/AgendaSuratTugas/Create');
    }

    public function store(StoreAgendaSuratTugasRequest $request): RedirectResponse
    {
        $this->authorize('create', AgendaSuratTugas::class);
        $this->createAction->execute($request->validated(), 'kecamatan');

        return redirect()
            ->route('kecamatan.agenda-surat-tugas.index')
            ->with('success', 'Agenda Surat Tugas berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/AgendaSuratTugas/Show', [
            'item' => [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat,
                'tanggal_surat' => $this->formatDateForPayload($item->tanggal_surat),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'lampiran' => $item->lampiran,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_url,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/AgendaSuratTugas/Edit', [
            'item' => [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat,
                'tanggal_surat' => $this->formatDateForPayload($item->tanggal_surat),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'lampiran' => $item->lampiran,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_url,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateAgendaSuratTugasRequest $request, int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        $this->updateAction->execute($item, $request->validated());

        return redirect()
            ->route('kecamatan.agenda-surat-tugas.index')
            ->with('success', 'Agenda Surat Tugas berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $item);

        $this->deleteAction->execute($item);

        return redirect()
            ->route('kecamatan.agenda-surat-tugas.index')
            ->with('success', 'Agenda Surat Tugas berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }
}
