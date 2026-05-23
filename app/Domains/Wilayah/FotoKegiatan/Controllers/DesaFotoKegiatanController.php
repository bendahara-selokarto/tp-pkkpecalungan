<?php

namespace App\Domains\Wilayah\FotoKegiatan\Controllers;

use App\Domains\Wilayah\FotoKegiatan\Actions\CreateScopedFotoKegiatanAction;
use App\Domains\Wilayah\FotoKegiatan\Actions\DeleteFotoKegiatanAction;
use App\Domains\Wilayah\FotoKegiatan\Actions\UpdateFotoKegiatanAction;
use App\Domains\Wilayah\FotoKegiatan\Models\FotoKegiatan;
use App\Domains\Wilayah\FotoKegiatan\Requests\StoreFotoKegiatanRequest;
use App\Domains\Wilayah\FotoKegiatan\Requests\UpdateFotoKegiatanRequest;
use App\Domains\Wilayah\FotoKegiatan\UseCases\GetFotoKegiatanUseCase;
use App\Domains\Wilayah\FotoKegiatan\UseCases\ListScopedFotoKegiatansUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaFotoKegiatanController extends Controller
{
    public function __construct(
        private readonly ListScopedFotoKegiatansUseCase $listScopedFotoKegiatansUseCase,
        private readonly GetFotoKegiatanUseCase $getFotoKegiatanUseCase,
        private readonly CreateScopedFotoKegiatanAction $createScopedFotoKegiatanAction,
        private readonly UpdateFotoKegiatanAction $updateFotoKegiatanAction,
        private readonly DeleteFotoKegiatanAction $deleteFotoKegiatanAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', FotoKegiatan::class);
        $fotoKegiatans = $this->listScopedFotoKegiatansUseCase->execute('desa', 12);

        return Inertia::render('Desa/FotoKegiatan/Index', [
            'fotoKegiatans' => $fotoKegiatans,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', FotoKegiatan::class);
        return Inertia::render('Desa/FotoKegiatan/Create');
    }

    public function store(StoreFotoKegiatanRequest $request): RedirectResponse
    {
        $this->authorize('create', FotoKegiatan::class);
        $this->createScopedFotoKegiatanAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.foto-kegiatan.index')->with('success', 'Foto kegiatan berhasil diunggah');
    }

    public function show(int $id): Response
    {
        $fotoKegiatan = $this->getFotoKegiatanUseCase->execute($id, 'desa');
        $this->authorize('view', $fotoKegiatan);

        return Inertia::render('Desa/FotoKegiatan/Show', [
            'fotoKegiatan' => $fotoKegiatan,
        ]);
    }

    public function edit(int $id): Response
    {
        $fotoKegiatan = $this->getFotoKegiatanUseCase->execute($id, 'desa');
        $this->authorize('update', $fotoKegiatan);

        return Inertia::render('Desa/FotoKegiatan/Edit', [
            'fotoKegiatan' => $fotoKegiatan,
        ]);
    }

    public function update(UpdateFotoKegiatanRequest $request, int $id): RedirectResponse
    {
        $fotoKegiatan = $this->getFotoKegiatanUseCase->execute($id, 'desa');
        $this->authorize('update', $fotoKegiatan);
        $this->updateFotoKegiatanAction->execute($fotoKegiatan, $request->validated());

        return redirect()->route('desa.foto-kegiatan.index')->with('success', 'Foto kegiatan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $fotoKegiatan = $this->getFotoKegiatanUseCase->execute($id, 'desa');
        $this->authorize('delete', $fotoKegiatan);
        $this->deleteFotoKegiatanAction->execute($fotoKegiatan);

        return redirect()->route('desa.foto-kegiatan.index')->with('success', 'Foto kegiatan berhasil dihapus');
    }
}
