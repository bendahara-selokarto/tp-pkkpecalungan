<?php

namespace App\Domains\Wilayah\Bkr\Controllers;

use App\Domains\Wilayah\Bkr\Actions\CreateScopedBkrAction;
use App\Domains\Wilayah\Bkr\Actions\UpdateBkrAction;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Bkr\Repositories\BkrRepositoryInterface;
use App\Domains\Wilayah\Bkr\Requests\StoreBkrRequest;
use App\Domains\Wilayah\Bkr\Requests\UpdateBkrRequest;
use App\Domains\Wilayah\Bkr\UseCases\GetScopedBkrUseCase;
use App\Domains\Wilayah\Bkr\UseCases\ListScopedBkrUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBkrController extends Controller
{
    public function __construct(
        private readonly BkrRepositoryInterface $bkrRepository,
        private readonly ListScopedBkrUseCase $listScopedBkrUseCase,
        private readonly GetScopedBkrUseCase $getScopedBkrUseCase,
        private readonly CreateScopedBkrAction $createScopedBkrAction,
        private readonly UpdateBkrAction $updateBkrAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Bkr::class);
        $items = $this->listScopedBkrUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/Bkr/Index', [
            'bkrItems' => $items->values()->map(fn (Bkr $item) => [
                'id' => $item->id,
                'desa' => $item->desa,
                'nama_bkr' => $item->nama_bkr,
                'no_tgl_sk' => $item->no_tgl_sk,
                'nama_ketua_kelompok' => $item->nama_ketua_kelompok,
                'jumlah_anggota' => $item->jumlah_anggota,
                'kegiatan' => $item->kegiatan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Bkr::class);

        return Inertia::render('Kecamatan/Bkr/Create');
    }

    public function store(StoreBkrRequest $request): RedirectResponse
    {
        $this->authorize('create', Bkr::class);
        $this->createScopedBkrAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.bkr.index')->with('success', 'Data BKR berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $bkr = $this->getScopedBkrUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $bkr);

        return Inertia::render('Kecamatan/Bkr/Show', [
            'bkr' => [
                'id' => $bkr->id,
                'desa' => $bkr->desa,
                'nama_bkr' => $bkr->nama_bkr,
                'no_tgl_sk' => $bkr->no_tgl_sk,
                'nama_ketua_kelompok' => $bkr->nama_ketua_kelompok,
                'jumlah_anggota' => $bkr->jumlah_anggota,
                'kegiatan' => $bkr->kegiatan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $bkr = $this->getScopedBkrUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkr);

        return Inertia::render('Kecamatan/Bkr/Edit', [
            'bkr' => [
                'id' => $bkr->id,
                'desa' => $bkr->desa,
                'nama_bkr' => $bkr->nama_bkr,
                'no_tgl_sk' => $bkr->no_tgl_sk,
                'nama_ketua_kelompok' => $bkr->nama_ketua_kelompok,
                'jumlah_anggota' => $bkr->jumlah_anggota,
                'kegiatan' => $bkr->kegiatan,
            ],
        ]);
    }

    public function update(UpdateBkrRequest $request, int $id): RedirectResponse
    {
        $bkr = $this->getScopedBkrUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkr);
        $this->updateBkrAction->execute($bkr, $request->validated());

        return redirect()->route('kecamatan.bkr.index')->with('success', 'Data BKR berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bkr = $this->getScopedBkrUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $bkr);
        $this->bkrRepository->delete($bkr);

        return redirect()->route('kecamatan.bkr.index')->with('success', 'Data BKR berhasil dihapus');
    }
}

