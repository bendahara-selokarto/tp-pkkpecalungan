<?php

namespace App\Domains\Wilayah\Simulasi\Controllers;

use App\Domains\Wilayah\Simulasi\Actions\CreateScopedBukuTamuSimulasiAction;
use App\Domains\Wilayah\Simulasi\Actions\UpdateBukuTamuSimulasiAction;
use App\Domains\Wilayah\Simulasi\Models\BukuTamuSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuTamuSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Requests\ListSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\StoreBukuTamuSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\UpdateBukuTamuSimulasiRequest;
use App\Domains\Wilayah\Simulasi\UseCases\GetScopedBukuTamuSimulasiUseCase;
use App\Domains\Wilayah\Simulasi\UseCases\ListScopedBukuTamuSimulasiUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaBukuTamuSimulasiController extends Controller
{
    public function __construct(
        private readonly BukuTamuSimulasiRepositoryInterface $repository,
        private readonly ListScopedBukuTamuSimulasiUseCase $listUseCase,
        private readonly GetScopedBukuTamuSimulasiUseCase $getUseCase,
        private readonly CreateScopedBukuTamuSimulasiAction $createAction,
        private readonly UpdateBukuTamuSimulasiAction $updateAction
    ) {
    }

    public function index(ListSimulasiRequest $request): Response
    {
        $this->authorize('viewAny', BukuTamuSimulasi::class);

        $items = $this->listUseCase->execute('desa', $request->perPage());

        return Inertia::render('Desa/Simulasi/BukuTamu/Index', [
            'items' => $items,
            'filters' => [
                'tahun_anggaran' => auth()->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuTamuSimulasi::class);

        return Inertia::render('Desa/Simulasi/BukuTamu/Create');
    }

    public function store(StoreBukuTamuSimulasiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuTamuSimulasi::class);

        $this->createAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.simulasi.buku-tamu.index')
            ->with('success', 'Buku tamu simulasi berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'desa');

        $this->authorize('view', $item);

        return Inertia::render('Desa/Simulasi/BukuTamu/Show', [
            'item' => [
                ...$item->toArray(),
                'file_url' => $item->file_url,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'desa');

        $this->authorize('update', $item);

        return Inertia::render('Desa/Simulasi/BukuTamu/Edit', [
            'item' => [
                ...$item->toArray(),
                'file_url' => $item->file_url,
            ],
        ]);
    }

    public function update(UpdateBukuTamuSimulasiRequest $request, int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'desa');

        $this->authorize('update', $item);

        $this->updateAction->execute($item, $request->validated());

        return redirect()->route('desa.simulasi.buku-tamu.index')
            ->with('success', 'Buku tamu simulasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'desa');

        $this->authorize('delete', $item);

        $this->repository->delete($item);

        return redirect()->route('desa.simulasi.buku-tamu.index')
            ->with('success', 'Buku tamu simulasi berhasil dihapus');
    }
}
