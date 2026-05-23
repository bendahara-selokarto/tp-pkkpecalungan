<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Controllers;

use App\Domains\Wilayah\BukuKonsultasi\Actions\CreateScopedBukuKonsultasiAction;
use App\Domains\Wilayah\BukuKonsultasi\Actions\DeleteBukuKonsultasiAction;
use App\Domains\Wilayah\BukuKonsultasi\Actions\UpdateBukuKonsultasiAction;
use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Requests\StoreBukuKonsultasiRequest;
use App\Domains\Wilayah\BukuKonsultasi\Requests\UpdateBukuKonsultasiRequest;
use App\Domains\Wilayah\BukuKonsultasi\UseCases\GetBukuKonsultasiUseCase;
use App\Domains\Wilayah\BukuKonsultasi\UseCases\ListScopedBukuKonsultasiesUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaBukuKonsultasiController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuKonsultasiesUseCase $listScopedBukuKonsultasiesUseCase,
        private readonly GetBukuKonsultasiUseCase $getBukuKonsultasiUseCase,
        private readonly CreateScopedBukuKonsultasiAction $createScopedBukuKonsultasiAction,
        private readonly UpdateBukuKonsultasiAction $updateBukuKonsultasiAction,
        private readonly DeleteBukuKonsultasiAction $deleteBukuKonsultasiAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', BukuKonsultasi::class);
        $bukuKonsultasies = $this->listScopedBukuKonsultasiesUseCase->execute('desa', 12);

        return Inertia::render('Desa/BukuKonsultasi/Index', [
            'bukuKonsultasies' => $bukuKonsultasies,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuKonsultasi::class);
        return Inertia::render('Desa/BukuKonsultasi/Create');
    }

    public function store(StoreBukuKonsultasiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuKonsultasi::class);
        $this->createScopedBukuKonsultasiAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil ditambahkan');
    }

    public function show(int $id): Response
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'desa');
        $this->authorize('view', $bukuKonsultasi);

        return Inertia::render('Desa/BukuKonsultasi/Show', [
            'bukuKonsultasi' => $bukuKonsultasi,
        ]);
    }

    public function edit(int $id): Response
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'desa');
        $this->authorize('update', $bukuKonsultasi);

        return Inertia::render('Desa/BukuKonsultasi/Edit', [
            'bukuKonsultasi' => $bukuKonsultasi,
        ]);
    }

    public function update(UpdateBukuKonsultasiRequest $request, int $id): RedirectResponse
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'desa');
        $this->authorize('update', $bukuKonsultasi);
        $this->updateBukuKonsultasiAction->execute($bukuKonsultasi, $request->validated());

        return redirect()->route('desa.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'desa');
        $this->authorize('delete', $bukuKonsultasi);
        $this->deleteBukuKonsultasiAction->execute($bukuKonsultasi);

        return redirect()->route('desa.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil dihapus');
    }
}
