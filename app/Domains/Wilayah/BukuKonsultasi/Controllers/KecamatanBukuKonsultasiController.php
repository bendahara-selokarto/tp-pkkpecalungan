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

class KecamatanBukuKonsultasiController extends Controller
{
    public function __construct(
        private readonly ListScopedBukuKonsultasiesUseCase $listScopedBukuKonsultasiesUseCase,
        private readonly GetBukuKonsultasiUseCase $getBukuKonsultasiUseCase,
        private readonly CreateScopedBukuKonsultasiAction $createScopedBukuKonsultasiAction,
        private readonly UpdateBukuKonsultasiAction $updateBukuKonsultasiAction,
        private readonly DeleteBukuKonsultasiAction $deleteBukuKonsultasiAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', BukuKonsultasi::class);
        $bukuKonsultasies = $this->listScopedBukuKonsultasiesUseCase->execute('kecamatan', 12);

        return Inertia::render('Kecamatan/BukuKonsultasi/Index', [
            'bukuKonsultasies' => $bukuKonsultasies,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BukuKonsultasi::class);
        return Inertia::render('Kecamatan/BukuKonsultasi/Create');
    }

    public function store(StoreBukuKonsultasiRequest $request): RedirectResponse
    {
        $this->authorize('create', BukuKonsultasi::class);
        $this->createScopedBukuKonsultasiAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil ditambahkan');
    }

    public function show(int $id): Response
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $bukuKonsultasi);

        return Inertia::render('Kecamatan/BukuKonsultasi/Show', [
            'bukuKonsultasi' => $bukuKonsultasi,
        ]);
    }

    public function edit(int $id): Response
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $bukuKonsultasi);

        return Inertia::render('Kecamatan/BukuKonsultasi/Edit', [
            'bukuKonsultasi' => $bukuKonsultasi,
        ]);
    }

    public function update(UpdateBukuKonsultasiRequest $request, int $id): RedirectResponse
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $bukuKonsultasi);
        $this->updateBukuKonsultasiAction->execute($bukuKonsultasi, $request->validated());

        return redirect()->route('kecamatan.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bukuKonsultasi = $this->getBukuKonsultasiUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $bukuKonsultasi);
        $this->deleteBukuKonsultasiAction->execute($bukuKonsultasi);

        return redirect()->route('kecamatan.buku-konsultasi.index')->with('success', 'Buku konsultasi berhasil dihapus');
    }
}
