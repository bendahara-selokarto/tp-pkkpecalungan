<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Controllers;

use App\Domains\Wilayah\BukuNotulenRapat\Actions\CreateScopedBukuNotulenRapatAction;
use App\Domains\Wilayah\BukuNotulenRapat\Actions\UpdateBukuNotulenRapatAction;
use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\BukuNotulenRapat\Repositories\BukuNotulenRapatRepositoryInterface;
use App\Domains\Wilayah\BukuNotulenRapat\Requests\ListBukuNotulenRapatRequest;
use App\Domains\Wilayah\BukuNotulenRapat\Requests\StoreBukuNotulenRapatRequest;
use App\Domains\Wilayah\BukuNotulenRapat\Requests\UpdateBukuNotulenRapatRequest;
use App\Domains\Wilayah\BukuNotulenRapat\UseCases\GetScopedBukuNotulenRapatUseCase;
use App\Domains\Wilayah\BukuNotulenRapat\UseCases\ListScopedBukuNotulenRapatUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuNotulenRapatController extends Controller
{
    public function __construct(
        private readonly BukuNotulenRapatRepositoryInterface $bukuNotulenRapatRepository,
        private readonly ListScopedBukuNotulenRapatUseCase $listScopedBukuNotulenRapatUseCase,
        private readonly GetScopedBukuNotulenRapatUseCase $getScopedBukuNotulenRapatUseCase,
        private readonly CreateScopedBukuNotulenRapatAction $createScopedBukuNotulenRapatAction,
        private readonly UpdateBukuNotulenRapatAction $updateBukuNotulenRapatAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListBukuNotulenRapatRequest $request): Response
    {
        $this->authorize('viewAny', BukuNotulenRapat::class);
        $items = $this->listScopedBukuNotulenRapatUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (BukuNotulenRapat $item) => [
                'id' => $item->id,
                'entry_date' => $this->formatDateForPayload($item->entry_date),
                'title' => $item->title,
                'person_name' => $item->person_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ]);

        return Inertia::render('Kecamatan/BukuNotulenRapat/Index', [
            'items' => $items,
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
        $this->authorize('create', BukuNotulenRapat::class);

        return Inertia::render('Kecamatan/BukuNotulenRapat/Create');
    }

    public function store(StoreBukuNotulenRapatRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuNotulenRapat::class);
        $this->createScopedBukuNotulenRapatAction->execute($request->validated(), 'kecamatan');

        return redirect()
            ->route('kecamatan.buku-notulen-rapat.index')
            ->with('success', 'Buku Notulen Rapat berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getScopedBukuNotulenRapatUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/BukuNotulenRapat/Show', [
            'item' => [
                'id' => $item->id,
                'entry_date' => $this->formatDateForPayload($item->entry_date),
                'title' => $item->title,
                'person_name' => $item->person_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedBukuNotulenRapatUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/BukuNotulenRapat/Edit', [
            'item' => [
                'id' => $item->id,
                'entry_date' => $this->formatDateForPayload($item->entry_date),
                'title' => $item->title,
                'person_name' => $item->person_name,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
        ]);
    }

    public function update(UpdateBukuNotulenRapatRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedBukuNotulenRapatUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);
        $this->updateBukuNotulenRapatAction->execute($item, $request->validated());

        return redirect()
            ->route('kecamatan.buku-notulen-rapat.index')
            ->with('success', 'Buku Notulen Rapat berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedBukuNotulenRapatUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $item);
        $this->bukuNotulenRapatRepository->delete($item);

        return redirect()
            ->route('kecamatan.buku-notulen-rapat.index')
            ->with('success', 'Buku Notulen Rapat berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }
}
