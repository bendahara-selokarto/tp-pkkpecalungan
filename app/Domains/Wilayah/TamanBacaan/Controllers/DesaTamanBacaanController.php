<?php

namespace App\Domains\Wilayah\TamanBacaan\Controllers;

use App\Domains\Wilayah\TamanBacaan\Actions\CreateScopedTamanBacaanAction;
use App\Domains\Wilayah\TamanBacaan\Actions\UpdateTamanBacaanAction;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;
use App\Domains\Wilayah\TamanBacaan\Requests\StoreTamanBacaanRequest;
use App\Domains\Wilayah\TamanBacaan\Requests\UpdateTamanBacaanRequest;
use App\Domains\Wilayah\TamanBacaan\UseCases\GetScopedTamanBacaanUseCase;
use App\Domains\Wilayah\TamanBacaan\UseCases\ListScopedTamanBacaanUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaTamanBacaanController extends Controller
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository,
        private readonly ListScopedTamanBacaanUseCase $listScopedTamanBacaanUseCase,
        private readonly GetScopedTamanBacaanUseCase $getScopedTamanBacaanUseCase,
        private readonly CreateScopedTamanBacaanAction $createScopedTamanBacaanAction,
        private readonly UpdateTamanBacaanAction $updateTamanBacaanAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', TamanBacaan::class);
        $items = $this->listScopedTamanBacaanUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/TamanBacaan/Index', [
            'tamanBacaanItems' => $items->values()->map(fn (TamanBacaan $item) => [
                'id' => $item->id,
                'nama_taman_bacaan' => $item->nama_taman_bacaan,
                'nama_pengelola' => $item->nama_pengelola,
                'jumlah_buku_bacaan' => $item->jumlah_buku_bacaan,
                'jenis_buku' => $item->jenis_buku,
                'kategori' => $item->kategori,
                'jumlah' => $item->jumlah,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', TamanBacaan::class);

        return Inertia::render('Desa/TamanBacaan/Create');
    }

    public function store(StoreTamanBacaanRequest $request): RedirectResponse
    {
        $this->authorize('create', TamanBacaan::class);
        $this->createScopedTamanBacaanAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.taman-bacaan.index')->with('success', 'Data taman bacaan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $tamanBacaan = $this->getScopedTamanBacaanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $tamanBacaan);

        return Inertia::render('Desa/TamanBacaan/Show', [
            'tamanBacaan' => [
                'id' => $tamanBacaan->id,
                'nama_taman_bacaan' => $tamanBacaan->nama_taman_bacaan,
                'nama_pengelola' => $tamanBacaan->nama_pengelola,
                'jumlah_buku_bacaan' => $tamanBacaan->jumlah_buku_bacaan,
                'jenis_buku' => $tamanBacaan->jenis_buku,
                'kategori' => $tamanBacaan->kategori,
                'jumlah' => $tamanBacaan->jumlah,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $tamanBacaan = $this->getScopedTamanBacaanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $tamanBacaan);

        return Inertia::render('Desa/TamanBacaan/Edit', [
            'tamanBacaan' => [
                'id' => $tamanBacaan->id,
                'nama_taman_bacaan' => $tamanBacaan->nama_taman_bacaan,
                'nama_pengelola' => $tamanBacaan->nama_pengelola,
                'jumlah_buku_bacaan' => $tamanBacaan->jumlah_buku_bacaan,
                'jenis_buku' => $tamanBacaan->jenis_buku,
                'kategori' => $tamanBacaan->kategori,
                'jumlah' => $tamanBacaan->jumlah,
            ],
        ]);
    }

    public function update(UpdateTamanBacaanRequest $request, int $id): RedirectResponse
    {
        $tamanBacaan = $this->getScopedTamanBacaanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $tamanBacaan);
        $this->updateTamanBacaanAction->execute($tamanBacaan, $request->validated());

        return redirect()->route('desa.taman-bacaan.index')->with('success', 'Data taman bacaan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tamanBacaan = $this->getScopedTamanBacaanUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $tamanBacaan);
        $this->tamanBacaanRepository->delete($tamanBacaan);

        return redirect()->route('desa.taman-bacaan.index')->with('success', 'Data taman bacaan berhasil dihapus');
    }
}


