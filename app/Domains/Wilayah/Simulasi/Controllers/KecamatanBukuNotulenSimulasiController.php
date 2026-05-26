<?php

namespace App\Domains\Wilayah\Simulasi\Controllers;

use App\Domains\Wilayah\Simulasi\Actions\CreateScopedBukuNotulenSimulasiAction;
use App\Domains\Wilayah\Simulasi\Actions\UpdateBukuNotulenSimulasiAction;
use App\Domains\Wilayah\Simulasi\Models\BukuNotulenSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuNotulenSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Requests\ListSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\StoreBukuNotulenSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\UpdateBukuNotulenSimulasiRequest;
use App\Domains\Wilayah\Simulasi\UseCases\GetScopedBukuNotulenSimulasiUseCase;
use App\Domains\Wilayah\Simulasi\UseCases\ListScopedBukuNotulenSimulasiUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuNotulenSimulasiController extends Controller
{
    public function __construct(
        private readonly BukuNotulenSimulasiRepositoryInterface $repository,
        private readonly ListScopedBukuNotulenSimulasiUseCase $listUseCase,
        private readonly GetScopedBukuNotulenSimulasiUseCase $getUseCase,
        private readonly CreateScopedBukuNotulenSimulasiAction $createAction,
        private readonly UpdateBukuNotulenSimulasiAction $updateAction
    ) {
    }

    public function index(ListSimulasiRequest $request): Response
    {
        $this->authorize('viewAny', BukuNotulenSimulasi::class);

        $items = $this->listUseCase->execute('kecamatan', $request->perPage());

        return Inertia::render('Kecamatan/Simulasi/BukuNotulen/Index', [
            'items' => $items,
            'filters' => [
                'tahun_anggaran' => auth()->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuNotulenSimulasi::class);

        return Inertia::render('Kecamatan/Simulasi/BukuNotulen/Create');
    }

    public function store(StoreBukuNotulenSimulasiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuNotulenSimulasi::class);

        $this->createAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.simulasi.buku-notulen.index')
            ->with('success', 'Buku notulen simulasi berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/Simulasi/BukuNotulen/Show', [
            'item' => [
                ...$item->toArray(),
                'file_url' => $item->file_url,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('update', $item);

        return Inertia::render('Kecamatan/Simulasi/BukuNotulen/Edit', [
            'item' => [
                ...$item->toArray(),
                'file_url' => $item->file_url,
            ],
        ]);
    }

    public function update(UpdateBukuNotulenSimulasiRequest $request, int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('update', $item);

        $this->updateAction->execute($item, $request->validated());

        return redirect()->route('kecamatan.simulasi.buku-notulen.index')
            ->with('success', 'Buku notulen simulasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('delete', $item);

        $this->repository->delete($item);

        return redirect()->route('kecamatan.simulasi.buku-notulen.index')
            ->with('success', 'Buku notulen simulasi berhasil dihapus');
    }
}
