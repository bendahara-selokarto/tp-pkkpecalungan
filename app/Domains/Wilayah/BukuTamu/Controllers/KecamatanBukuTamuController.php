<?php

namespace App\Domains\Wilayah\BukuTamu\Controllers;

use App\Domains\Wilayah\BukuTamu\Actions\CreateScopedBukuTamuAction;
use App\Domains\Wilayah\BukuTamu\Actions\UpdateBukuTamuAction;
use App\Domains\Wilayah\BukuTamu\Models\BukuTamu;
use App\Domains\Wilayah\BukuTamu\Repositories\BukuTamuRepositoryInterface;
use App\Domains\Wilayah\BukuTamu\Requests\ListBukuTamuRequest;
use App\Domains\Wilayah\BukuTamu\Requests\StoreBukuTamuRequest;
use App\Domains\Wilayah\BukuTamu\Requests\UpdateBukuTamuRequest;
use App\Domains\Wilayah\BukuTamu\UseCases\GetScopedBukuTamuUseCase;
use App\Domains\Wilayah\BukuTamu\UseCases\ListScopedBukuTamuUseCase;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuTamuController extends Controller
{
    public function __construct(
        private readonly BukuTamuRepositoryInterface $bukuTamuRepository,
        private readonly ListScopedBukuTamuUseCase $listScopedBukuTamuUseCase,
        private readonly GetScopedBukuTamuUseCase $getScopedBukuTamuUseCase,
        private readonly CreateScopedBukuTamuAction $createScopedBukuTamuAction,
        private readonly UpdateBukuTamuAction $updateBukuTamuAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListBukuTamuRequest $request): Response
    {
        $this->authorize('viewAny', BukuTamu::class);
        $items = $this->listScopedBukuTamuUseCase
            ->execute('kecamatan', $request->perPage())
            ->through(fn (BukuTamu $item) => [
                'id' => $item->id,
                'visit_date' => $this->formatDateForPayload($item->visit_date),
                'guest_name' => $item->guest_name,
                'purpose' => $item->purpose,
                'institution' => $item->institution,
                'description' => $item->description,
            ]);

        return Inertia::render('Kecamatan/BukuTamu/Index', [
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
        $this->authorize('create', BukuTamu::class);

        return Inertia::render('Kecamatan/BukuTamu/Create');
    }

    public function store(StoreBukuTamuRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuTamu::class);
        $this->createScopedBukuTamuAction->execute($request->validated(), 'kecamatan');

        return redirect()
            ->route('kecamatan.buku-tamu.index')
            ->with('success', 'Buku Tamu berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $item = $this->getScopedBukuTamuUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/BukuTamu/Show', [
            'item' => [
                'id' => $item->id,
                'visit_date' => $this->formatDateForPayload($item->visit_date),
                'guest_name' => $item->guest_name,
                'purpose' => $item->purpose,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getScopedBukuTamuUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/BukuTamu/Edit', [
            'item' => [
                'id' => $item->id,
                'visit_date' => $this->formatDateForPayload($item->visit_date),
                'guest_name' => $item->guest_name,
                'purpose' => $item->purpose,
                'institution' => $item->institution,
                'description' => $item->description,
            ],
        ]);
    }

    public function update(UpdateBukuTamuRequest $request, int $id): RedirectResponse
    {
        $item = $this->getScopedBukuTamuUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $item);
        $this->updateBukuTamuAction->execute($item, $request->validated());

        return redirect()
            ->route('kecamatan.buku-tamu.index')
            ->with('success', 'Buku Tamu berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getScopedBukuTamuUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $item);
        $this->bukuTamuRepository->delete($item);

        return redirect()
            ->route('kecamatan.buku-tamu.index')
            ->with('success', 'Buku Tamu berhasil dihapus');
    }

    private function formatDateForPayload(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }
}
