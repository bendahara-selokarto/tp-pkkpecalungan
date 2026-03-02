<?php

namespace App\Domains\Wilayah\KejarPaket\Controllers;

use App\Domains\Wilayah\KejarPaket\Actions\CreateScopedKejarPaketAction;
use App\Domains\Wilayah\KejarPaket\Actions\UpdateKejarPaketAction;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;
use App\Domains\Wilayah\KejarPaket\Requests\StoreKejarPaketRequest;
use App\Domains\Wilayah\KejarPaket\Requests\ListKejarPaketRequest;
use App\Domains\Wilayah\KejarPaket\Requests\UpdateKejarPaketRequest;
use App\Domains\Wilayah\KejarPaket\UseCases\GetScopedKejarPaketUseCase;
use App\Domains\Wilayah\KejarPaket\UseCases\ListScopedKejarPaketUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaKejarPaketController extends Controller
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository,
        private readonly ListScopedKejarPaketUseCase $listScopedKejarPaketUseCase,
        private readonly GetScopedKejarPaketUseCase $getScopedKejarPaketUseCase,
        private readonly CreateScopedKejarPaketAction $createScopedKejarPaketAction,
        private readonly UpdateKejarPaketAction $updateKejarPaketAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListKejarPaketRequest $request): Response
    {
        $this->authorize('viewAny', KejarPaket::class);
        $items = $this->listScopedKejarPaketUseCase->execute(ScopeLevel::DESA->value, $request->perPage());

        return Inertia::render('Desa/KejarPaket/Index', [
            'kejarPaketItems' => $items->through(fn (KejarPaket $item) => [
                'id' => $item->id,
                'nama_kejar_paket' => $item->nama_kejar_paket,
                'jenis_kejar_paket' => $item->jenis_kejar_paket,
                'jumlah_warga_belajar_l' => $item->jumlah_warga_belajar_l,
                'jumlah_warga_belajar_p' => $item->jumlah_warga_belajar_p,
                'jumlah_pengajar_l' => $item->jumlah_pengajar_l,
                'jumlah_pengajar_p' => $item->jumlah_pengajar_p,
            ]),
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
        $this->authorize('create', KejarPaket::class);

        return Inertia::render('Desa/KejarPaket/Create');
    }

    public function store(StoreKejarPaketRequest $request): RedirectResponse
    {
        $this->authorize('create', KejarPaket::class);
        $this->createScopedKejarPaketAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.kejar-paket.index')->with('success', 'Data kejar paket berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $kejarPaket = $this->getScopedKejarPaketUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $kejarPaket);

        return Inertia::render('Desa/KejarPaket/Show', [
            'kejarPaket' => [
                'id' => $kejarPaket->id,
                'nama_kejar_paket' => $kejarPaket->nama_kejar_paket,
                'jenis_kejar_paket' => $kejarPaket->jenis_kejar_paket,
                'jumlah_warga_belajar_l' => $kejarPaket->jumlah_warga_belajar_l,
                'jumlah_warga_belajar_p' => $kejarPaket->jumlah_warga_belajar_p,
                'jumlah_pengajar_l' => $kejarPaket->jumlah_pengajar_l,
                'jumlah_pengajar_p' => $kejarPaket->jumlah_pengajar_p,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $kejarPaket = $this->getScopedKejarPaketUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $kejarPaket);

        return Inertia::render('Desa/KejarPaket/Edit', [
            'kejarPaket' => [
                'id' => $kejarPaket->id,
                'nama_kejar_paket' => $kejarPaket->nama_kejar_paket,
                'jenis_kejar_paket' => $kejarPaket->jenis_kejar_paket,
                'jumlah_warga_belajar_l' => $kejarPaket->jumlah_warga_belajar_l,
                'jumlah_warga_belajar_p' => $kejarPaket->jumlah_warga_belajar_p,
                'jumlah_pengajar_l' => $kejarPaket->jumlah_pengajar_l,
                'jumlah_pengajar_p' => $kejarPaket->jumlah_pengajar_p,
            ],
        ]);
    }

    public function update(UpdateKejarPaketRequest $request, int $id): RedirectResponse
    {
        $kejarPaket = $this->getScopedKejarPaketUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $kejarPaket);
        $this->updateKejarPaketAction->execute($kejarPaket, $request->validated());

        return redirect()->route('desa.kejar-paket.index')->with('success', 'Data kejar paket berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kejarPaket = $this->getScopedKejarPaketUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $kejarPaket);
        $this->kejarPaketRepository->delete($kejarPaket);

        return redirect()->route('desa.kejar-paket.index')->with('success', 'Data kejar paket berhasil dihapus');
    }
}




