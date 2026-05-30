<?php

namespace App\Domains\Wilayah\Kliping\Controllers;

use App\Domains\Wilayah\Kliping\Actions\CreateScopedKlipingAction;
use App\Domains\Wilayah\Kliping\Actions\UpdateKlipingAction;
use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Kliping\Repositories\KlipingRepositoryInterface;
use App\Domains\Wilayah\Kliping\Requests\ListKlipingRequest;
use App\Domains\Wilayah\Kliping\Requests\StoreKlipingRequest;
use App\Domains\Wilayah\Kliping\Requests\UpdateKlipingRequest;
use App\Domains\Wilayah\Kliping\UseCases\GetScopedKlipingUseCase;
use App\Domains\Wilayah\Kliping\UseCases\ListScopedKlipingUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaKlipingController extends Controller
{
    public function __construct(
        private readonly KlipingRepositoryInterface $klipingRepository,
        private readonly ListScopedKlipingUseCase $listScopedKlipingUseCase,
        private readonly GetScopedKlipingUseCase $getScopedKlipingUseCase,
        private readonly CreateScopedKlipingAction $createScopedKlipingAction,
        private readonly UpdateKlipingAction $updateKlipingAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListKlipingRequest $request): Response
    {
        $this->authorize('viewAny', Kliping::class);
        $items = $this->listScopedKlipingUseCase
            ->execute('desa', $request->perPage())
            ->through(fn (Kliping $item) => [
                'id' => $item->id,
                'date' => $this->formatDateForPayload($item->date),
                'description' => $item->description,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
            ]);

        return Inertia::render('Desa/Kliping/Index', [
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
        $this->authorize('create', Kliping::class);

        return Inertia::render('Desa/Kliping/Create');
    }

    public function store(StoreKlipingRequest $request): RedirectResponse
    {
        $this->authorize('create', Kliping::class);
        $this->createScopedKlipingAction->execute($request->validated(), 'desa');

        return redirect()
            ->route('desa.buku-kliping.index')
            ->with('success', 'Buku Kliping berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getScopedKlipingUseCase->execute($id, 'desa');
        $this->authorize('view', $item);

        return Inertia::render('Desa/Kliping/Show', [
            'item' => [
                'id' => $item->id,
                'date' => $this->formatDateForPayload($item->date),
                'description' => $item->description,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedKlipingUseCase->execute($id, 'desa');
        $this->authorize('update', $item);

        return Inertia::render('Desa/Kliping/Edit', [
            'item' => [
                'id' => $item->id,
                'date' => $this->formatDateForPayload($item->date),
                'description' => $item->description,
                'file_url' => $item->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->file_path) : null,
                'tahun_anggaran' => $item->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateKlipingRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedKlipingUseCase->execute($id, 'desa');
        $this->authorize('update', $item);
        $this->updateKlipingAction->execute($item, $request->validated());

        return redirect()
            ->route('desa.buku-kliping.index')
            ->with('success', 'Buku Kliping berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedKlipingUseCase->execute($id, 'desa');
        $this->authorize('delete', $item);
        $this->klipingRepository->delete($item);

        return redirect()
            ->route('desa.buku-kliping.index')
            ->with('success', 'Buku Kliping berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }
}
