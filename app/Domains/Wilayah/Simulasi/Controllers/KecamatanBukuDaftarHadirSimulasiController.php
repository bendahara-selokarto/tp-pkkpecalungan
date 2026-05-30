<?php

namespace App\Domains\Wilayah\Simulasi\Controllers;

use App\Domains\Wilayah\Simulasi\Actions\CreateScopedBukuDaftarHadirSimulasiAction;
use App\Domains\Wilayah\Simulasi\Actions\UpdateBukuDaftarHadirSimulasiAction;
use App\Domains\Wilayah\Simulasi\Models\BukuDaftarHadirSimulasi;
use App\Domains\Wilayah\Simulasi\Repositories\BukuDaftarHadirSimulasiRepositoryInterface;
use App\Domains\Wilayah\Simulasi\Requests\ListSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\StoreBukuDaftarHadirSimulasiRequest;
use App\Domains\Wilayah\Simulasi\Requests\UpdateBukuDaftarHadirSimulasiRequest;
use App\Domains\Wilayah\Simulasi\UseCases\GetScopedBukuDaftarHadirSimulasiUseCase;
use App\Domains\Wilayah\Simulasi\UseCases\ListScopedBukuDaftarHadirSimulasiUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBukuDaftarHadirSimulasiController extends Controller
{
    public function __construct(
        private readonly BukuDaftarHadirSimulasiRepositoryInterface $repository,
        private readonly ListScopedBukuDaftarHadirSimulasiUseCase $listUseCase,
        private readonly GetScopedBukuDaftarHadirSimulasiUseCase $getUseCase,
        private readonly CreateScopedBukuDaftarHadirSimulasiAction $createAction,
        private readonly UpdateBukuDaftarHadirSimulasiAction $updateAction
    ) {
    }

    public function index(ListSimulasiRequest $request): Response
    {
        $this->authorize('viewAny', BukuDaftarHadirSimulasi::class);

        $items = $this->listUseCase->execute('kecamatan', $request->perPage());

        return Inertia::render('Kecamatan/Simulasi/BukuDaftarHadir/Index', [
            'items' => $items,
            'filters' => [
                'tahun_anggaran' => auth()->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuDaftarHadirSimulasi::class);

        return Inertia::render('Kecamatan/Simulasi/BukuDaftarHadir/Create');
    }

    public function store(StoreBukuDaftarHadirSimulasiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuDaftarHadirSimulasi::class);

        $this->createAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.simulasi.buku-daftar-hadir.index')
            ->with('success', 'Buku daftar hadir simulasi berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('view', $item);

        return Inertia::render('Kecamatan/Simulasi/BukuDaftarHadir/Show', [
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

        return Inertia::render('Kecamatan/Simulasi/BukuDaftarHadir/Edit', [
            'item' => [
                ...$item->toArray(),
                'file_url' => $item->file_url,
            ],
        ]);
    }

    public function update(UpdateBukuDaftarHadirSimulasiRequest $request, int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('update', $item);

        $this->updateAction->execute($item, $request->validated());

        return redirect()->route('kecamatan.simulasi.buku-daftar-hadir.index')
            ->with('success', 'Buku daftar hadir simulasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->getUseCase->execute($id, 'kecamatan');

        $this->authorize('delete', $item);

        $this->repository->delete($item);

        return redirect()->route('kecamatan.simulasi.buku-daftar-hadir.index')
            ->with('success', 'Buku daftar hadir simulasi berhasil dihapus');
    }
}
