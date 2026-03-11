<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Actions\CreateScopedPelatihanKaderPokjaIiAction;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Actions\UpdatePelatihanKaderPokjaIiAction;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories\PelatihanKaderPokjaIiRepositoryInterface;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Requests\ListPelatihanKaderPokjaIiRequest;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Requests\StorePelatihanKaderPokjaIiRequest;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Requests\UpdatePelatihanKaderPokjaIiRequest;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\UseCases\GetScopedPelatihanKaderPokjaIiUseCase;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\UseCases\ListScopedPelatihanKaderPokjaIiUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaPelatihanKaderPokjaIiController extends Controller
{
    public function __construct(
        private readonly PelatihanKaderPokjaIiRepositoryInterface $pelatihanKaderPokjaIiRepository,
        private readonly ListScopedPelatihanKaderPokjaIiUseCase $listScopedPelatihanKaderPokjaIiUseCase,
        private readonly GetScopedPelatihanKaderPokjaIiUseCase $getScopedPelatihanKaderPokjaIiUseCase,
        private readonly CreateScopedPelatihanKaderPokjaIiAction $createScopedPelatihanKaderPokjaIiAction,
        private readonly UpdatePelatihanKaderPokjaIiAction $updatePelatihanKaderPokjaIiAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListPelatihanKaderPokjaIiRequest $request): Response
    {
        $this->authorize('viewAny', PelatihanKaderPokjaIi::class);
        $items = $this->listScopedPelatihanKaderPokjaIiUseCase->execute(ScopeLevel::DESA->value, $request->perPage());

        return Inertia::render('Desa/PelatihanKaderPokjaIi/Index', [
            'pelatihanKaderItems' => $items->through(fn (PelatihanKaderPokjaIi $item) => [
                'id' => $item->id,
                'kategori_pelatihan' => $item->kategori_pelatihan,
                'jumlah_kader' => $item->jumlah_kader,
                'keterangan' => $item->keterangan,
                'tahun_anggaran' => $item->tahun_anggaran,
            ]),
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'tahun_anggaran' => (int) $request->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', PelatihanKaderPokjaIi::class);

        return Inertia::render('Desa/PelatihanKaderPokjaIi/Create');
    }

    public function store(StorePelatihanKaderPokjaIiRequest $request): RedirectResponse
    {
        $this->authorize('create', PelatihanKaderPokjaIi::class);
        $this->createScopedPelatihanKaderPokjaIiAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.pelatihan-kader-pokja-ii.index')->with('success', 'Data pelatihan kader Pokja II berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $pelatihanKader = $this->getScopedPelatihanKaderPokjaIiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $pelatihanKader);

        return Inertia::render('Desa/PelatihanKaderPokjaIi/Show', [
            'pelatihanKader' => [
                'id' => $pelatihanKader->id,
                'kategori_pelatihan' => $pelatihanKader->kategori_pelatihan,
                'jumlah_kader' => $pelatihanKader->jumlah_kader,
                'keterangan' => $pelatihanKader->keterangan,
                'tahun_anggaran' => $pelatihanKader->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $pelatihanKader = $this->getScopedPelatihanKaderPokjaIiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $pelatihanKader);

        return Inertia::render('Desa/PelatihanKaderPokjaIi/Edit', [
            'pelatihanKader' => [
                'id' => $pelatihanKader->id,
                'kategori_pelatihan' => $pelatihanKader->kategori_pelatihan,
                'jumlah_kader' => $pelatihanKader->jumlah_kader,
                'keterangan' => $pelatihanKader->keterangan,
                'tahun_anggaran' => $pelatihanKader->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdatePelatihanKaderPokjaIiRequest $request, int $id): RedirectResponse
    {
        $pelatihanKader = $this->getScopedPelatihanKaderPokjaIiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $pelatihanKader);
        $this->updatePelatihanKaderPokjaIiAction->execute($pelatihanKader, $request->validated());

        return redirect()->route('desa.pelatihan-kader-pokja-ii.index')->with('success', 'Data pelatihan kader Pokja II berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $pelatihanKader = $this->getScopedPelatihanKaderPokjaIiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $pelatihanKader);
        $this->pelatihanKaderPokjaIiRepository->delete($pelatihanKader);

        return redirect()->route('desa.pelatihan-kader-pokja-ii.index')->with('success', 'Data pelatihan kader Pokja II berhasil dihapus');
    }
}
