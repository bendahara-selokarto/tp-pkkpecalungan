<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Controllers;

use App\Domains\Wilayah\BukuAgendaSk\Actions\CreateScopedBukuAgendaSkAction;
use App\Domains\Wilayah\BukuAgendaSk\Actions\DeleteBukuAgendaSkAction;
use App\Domains\Wilayah\BukuAgendaSk\Actions\UpdateBukuAgendaSkAction;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use App\Domains\Wilayah\BukuAgendaSk\Requests\ListBukuAgendaSkRequest;
use App\Domains\Wilayah\BukuAgendaSk\Requests\StoreBukuAgendaSkRequest;
use App\Domains\Wilayah\BukuAgendaSk\Requests\UpdateBukuAgendaSkRequest;
use App\Domains\Wilayah\BukuAgendaSk\UseCases\GetScopedBukuAgendaSkUseCase;
use App\Domains\Wilayah\BukuAgendaSk\UseCases\ListScopedBukuAgendaSkUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuAgendaSkController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuAgendaSkUseCase $listScopedBukuAgendaSkUseCase,
        private readonly GetScopedBukuAgendaSkUseCase $getScopedBukuAgendaSkUseCase,
        private readonly CreateScopedBukuAgendaSkAction $createScopedBukuAgendaSkAction,
        private readonly UpdateBukuAgendaSkAction $updateBukuAgendaSkAction,
        private readonly DeleteBukuAgendaSkAction $deleteBukuAgendaSkAction
    ) {
    }

    public function index(ListBukuAgendaSkRequest $request): Response
    {
        $this->authorize('viewAny', BukuAgendaSk::class);
        $items = $this->listScopedBukuAgendaSkUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (BukuAgendaSk $item) => [
                'id' => $item->id,
                'nomor_sk' => $item->nomor_sk,
                'tanggal_sk' => $this->formatDateForPayload($item->tanggal_sk),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
            ]);

        return Inertia::render('Kecamatan/BukuAgendaSk/Index', [
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
        $this->authorize('create', BukuAgendaSk::class);

        return Inertia::render('Kecamatan/BukuAgendaSk/Create');
    }

    public function store(StoreBukuAgendaSkRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuAgendaSk::class);
        $this->createScopedBukuAgendaSkAction->execute($request->validated(), 'kecamatan');

        return redirect()
            ->route('kecamatan.buku-agenda-sk.index')
            ->with('success', 'Buku Agenda SK berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getScopedBukuAgendaSkUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/BukuAgendaSk/Show', [
            'item' => [
                'id' => $item->id,
                'nomor_sk' => $item->nomor_sk,
                'tanggal_sk' => $this->formatDateForPayload($item->tanggal_sk),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedBukuAgendaSkUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/BukuAgendaSk/Edit', [
            'item' => [
                'id' => $item->id,
                'nomor_sk' => $item->nomor_sk,
                'tanggal_sk' => $this->formatDateForPayload($item->tanggal_sk),
                'kepada' => $item->kepada,
                'perihal' => $item->perihal,
                'tembusan' => $item->tembusan,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateBukuAgendaSkRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedBukuAgendaSkUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        $this->updateBukuAgendaSkAction->execute($item, $request->validated());

        return redirect()
            ->route('kecamatan.buku-agenda-sk.index')
            ->with('success', 'Buku Agenda SK berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedBukuAgendaSkUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $item);

        $this->deleteBukuAgendaSkAction->execute($item);

        return redirect()
            ->route('kecamatan.buku-agenda-sk.index')
            ->with('success', 'Buku Agenda SK berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }
}
