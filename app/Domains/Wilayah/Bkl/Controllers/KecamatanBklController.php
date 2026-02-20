<?php

namespace App\Domains\Wilayah\Bkl\Controllers;

use App\Domains\Wilayah\Bkl\Actions\CreateScopedBklAction;
use App\Domains\Wilayah\Bkl\Actions\UpdateBklAction;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Bkl\Repositories\BklRepositoryInterface;
use App\Domains\Wilayah\Bkl\Requests\StoreBklRequest;
use App\Domains\Wilayah\Bkl\Requests\UpdateBklRequest;
use App\Domains\Wilayah\Bkl\UseCases\GetScopedBklUseCase;
use App\Domains\Wilayah\Bkl\UseCases\ListScopedBklUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBklController extends Controller
{
    public function __construct(
        private readonly BklRepositoryInterface $bklRepository,
        private readonly ListScopedBklUseCase $listScopedBklUseCase,
        private readonly GetScopedBklUseCase $getScopedBklUseCase,
        private readonly CreateScopedBklAction $createScopedBklAction,
        private readonly UpdateBklAction $updateBklAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Bkl::class);
        $items = $this->listScopedBklUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/Bkl/Index', [
            'bklItems' => $items->values()->map(fn (Bkl $item) => [
                'id' => $item->id,
                'desa' => $item->desa,
                'nama_bkl' => $item->nama_bkl,
                'no_tgl_sk' => $item->no_tgl_sk,
                'nama_ketua_kelompok' => $item->nama_ketua_kelompok,
                'jumlah_anggota' => $item->jumlah_anggota,
                'kegiatan' => $item->kegiatan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Bkl::class);

        return Inertia::render('Kecamatan/Bkl/Create');
    }

    public function store(StoreBklRequest $request): RedirectResponse
    {
        $this->authorize('create', Bkl::class);
        $this->createScopedBklAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.bkl.index')->with('success', 'Data BKL berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $bkl = $this->getScopedBklUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $bkl);

        return Inertia::render('Kecamatan/Bkl/Show', [
            'bkl' => [
                'id' => $bkl->id,
                'desa' => $bkl->desa,
                'nama_bkl' => $bkl->nama_bkl,
                'no_tgl_sk' => $bkl->no_tgl_sk,
                'nama_ketua_kelompok' => $bkl->nama_ketua_kelompok,
                'jumlah_anggota' => $bkl->jumlah_anggota,
                'kegiatan' => $bkl->kegiatan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $bkl = $this->getScopedBklUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkl);

        return Inertia::render('Kecamatan/Bkl/Edit', [
            'bkl' => [
                'id' => $bkl->id,
                'desa' => $bkl->desa,
                'nama_bkl' => $bkl->nama_bkl,
                'no_tgl_sk' => $bkl->no_tgl_sk,
                'nama_ketua_kelompok' => $bkl->nama_ketua_kelompok,
                'jumlah_anggota' => $bkl->jumlah_anggota,
                'kegiatan' => $bkl->kegiatan,
            ],
        ]);
    }

    public function update(UpdateBklRequest $request, int $id): RedirectResponse
    {
        $bkl = $this->getScopedBklUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkl);
        $this->updateBklAction->execute($bkl, $request->validated());

        return redirect()->route('kecamatan.bkl.index')->with('success', 'Data BKL berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bkl = $this->getScopedBklUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $bkl);
        $this->bklRepository->delete($bkl);

        return redirect()->route('kecamatan.bkl.index')->with('success', 'Data BKL berhasil dihapus');
    }
}
