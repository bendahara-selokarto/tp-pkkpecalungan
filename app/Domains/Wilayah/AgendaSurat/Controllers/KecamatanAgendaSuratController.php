<?php

namespace App\Domains\Wilayah\AgendaSurat\Controllers;

use App\Domains\Wilayah\AgendaSurat\Actions\CreateScopedAgendaSuratAction;
use App\Domains\Wilayah\AgendaSurat\Actions\UpdateAgendaSuratAction;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use App\Domains\Wilayah\AgendaSurat\Repositories\AgendaSuratRepositoryInterface;
use App\Domains\Wilayah\AgendaSurat\Requests\ListAgendaSuratRequest;
use App\Domains\Wilayah\AgendaSurat\Requests\StoreAgendaSuratRequest;
use App\Domains\Wilayah\AgendaSurat\Requests\UpdateAgendaSuratRequest;
use App\Domains\Wilayah\AgendaSurat\UseCases\GetScopedAgendaSuratUseCase;
use App\Domains\Wilayah\AgendaSurat\UseCases\ListScopedAgendaSuratUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanAgendaSuratController extends Controller
{
    public function __construct(
        private readonly AgendaSuratRepositoryInterface $agendaSuratRepository,
        private readonly ListScopedAgendaSuratUseCase $listScopedAgendaSuratUseCase,
        private readonly GetScopedAgendaSuratUseCase $getScopedAgendaSuratUseCase,
        private readonly CreateScopedAgendaSuratAction $createScopedAgendaSuratAction,
        private readonly UpdateAgendaSuratAction $updateAgendaSuratAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListAgendaSuratRequest $request): Response
    {
        $this->authorize('viewAny', AgendaSurat::class);
        $items = $this->listScopedAgendaSuratUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (AgendaSurat $item) => $this->toArray($item));

        return Inertia::render('Kecamatan/AgendaSurat/Index', [
            'agendaSurats' => $items,
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
        $this->authorize('create', AgendaSurat::class);

        return Inertia::render('Kecamatan/AgendaSurat/Create');
    }

    public function store(StoreAgendaSuratRequest $request): RedirectResponse
    {
        $this->authorize('create', AgendaSurat::class);
        $this->createScopedAgendaSuratAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.agenda-surat.index')->with('success', 'Agenda surat berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $agendaSurat = $this->getScopedAgendaSuratUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $agendaSurat);

        return Inertia::render('Kecamatan/AgendaSurat/Show', [
            'agendaSurat' => $this->toArray($agendaSurat),
        ]);
    }

    public function edit(int $id): Response
    {
        $agendaSurat = $this->getScopedAgendaSuratUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $agendaSurat);

        return Inertia::render('Kecamatan/AgendaSurat/Edit', [
            'agendaSurat' => $this->toArray($agendaSurat),
        ]);
    }

    public function update(UpdateAgendaSuratRequest $request, int $id): RedirectResponse
    {
        $agendaSurat = $this->getScopedAgendaSuratUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $agendaSurat);
        $this->updateAgendaSuratAction->execute($agendaSurat, $request->validated());

        return redirect()->route('kecamatan.agenda-surat.index')->with('success', 'Agenda surat berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $agendaSurat = $this->getScopedAgendaSuratUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $agendaSurat);
        $this->agendaSuratRepository->delete($agendaSurat);

        return redirect()->route('kecamatan.agenda-surat.index')->with('success', 'Agenda surat berhasil dihapus');
    }

    private function toArray(AgendaSurat $item): array
    {
        return [
            'id' => $item->id,
            'jenis_surat' => $item->jenis_surat,
            'tanggal_terima' => $this->formatDateForPayload($item->tanggal_terima),
            'tanggal_surat' => $this->formatDateForPayload($item->tanggal_surat),
            'nomor_surat' => $item->nomor_surat,
            'asal_surat' => $item->asal_surat,
            'dari' => $item->dari,
            'kepada' => $item->kepada,
            'perihal' => $item->perihal,
            'lampiran' => $item->lampiran,
            'diteruskan_kepada' => $item->diteruskan_kepada,
            'tembusan' => $item->tembusan,
            'keterangan' => $item->keterangan,
        ];
    }

    private function formatDateForPayload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }
}
