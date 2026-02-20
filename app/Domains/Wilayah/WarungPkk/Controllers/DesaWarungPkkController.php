<?php

namespace App\Domains\Wilayah\WarungPkk\Controllers;

use App\Domains\Wilayah\WarungPkk\Actions\CreateScopedWarungPkkAction;
use App\Domains\Wilayah\WarungPkk\Actions\UpdateWarungPkkAction;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Domains\Wilayah\WarungPkk\Repositories\WarungPkkRepositoryInterface;
use App\Domains\Wilayah\WarungPkk\Requests\StoreWarungPkkRequest;
use App\Domains\Wilayah\WarungPkk\Requests\UpdateWarungPkkRequest;
use App\Domains\Wilayah\WarungPkk\UseCases\GetScopedWarungPkkUseCase;
use App\Domains\Wilayah\WarungPkk\UseCases\ListScopedWarungPkkUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaWarungPkkController extends Controller
{
    public function __construct(
        private readonly WarungPkkRepositoryInterface $warungPkkRepository,
        private readonly ListScopedWarungPkkUseCase $listScopedWarungPkkUseCase,
        private readonly GetScopedWarungPkkUseCase $getScopedWarungPkkUseCase,
        private readonly CreateScopedWarungPkkAction $createScopedWarungPkkAction,
        private readonly UpdateWarungPkkAction $updateWarungPkkAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', WarungPkk::class);
        $items = $this->listScopedWarungPkkUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/WarungPkk/Index', [
            'warungPkkItems' => $items->values()->map(fn (WarungPkk $item) => [
                'id' => $item->id,
                'nama_warung_pkk' => $item->nama_warung_pkk,
                'nama_pengelola' => $item->nama_pengelola,
                'komoditi' => $item->komoditi,
                'kategori' => $item->kategori,
                'volume' => $item->volume,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', WarungPkk::class);

        return Inertia::render('Desa/WarungPkk/Create');
    }

    public function store(StoreWarungPkkRequest $request): RedirectResponse
    {
        $this->authorize('create', WarungPkk::class);
        $this->createScopedWarungPkkAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.warung-pkk.index')->with('success', 'Data warung PKK berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $warungPkk = $this->getScopedWarungPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $warungPkk);

        return Inertia::render('Desa/WarungPkk/Show', [
            'warungPkk' => [
                'id' => $warungPkk->id,
                'nama_warung_pkk' => $warungPkk->nama_warung_pkk,
                'nama_pengelola' => $warungPkk->nama_pengelola,
                'komoditi' => $warungPkk->komoditi,
                'kategori' => $warungPkk->kategori,
                'volume' => $warungPkk->volume,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $warungPkk = $this->getScopedWarungPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $warungPkk);

        return Inertia::render('Desa/WarungPkk/Edit', [
            'warungPkk' => [
                'id' => $warungPkk->id,
                'nama_warung_pkk' => $warungPkk->nama_warung_pkk,
                'nama_pengelola' => $warungPkk->nama_pengelola,
                'komoditi' => $warungPkk->komoditi,
                'kategori' => $warungPkk->kategori,
                'volume' => $warungPkk->volume,
            ],
        ]);
    }

    public function update(UpdateWarungPkkRequest $request, int $id): RedirectResponse
    {
        $warungPkk = $this->getScopedWarungPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $warungPkk);
        $this->updateWarungPkkAction->execute($warungPkk, $request->validated());

        return redirect()->route('desa.warung-pkk.index')->with('success', 'Data warung PKK berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $warungPkk = $this->getScopedWarungPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $warungPkk);
        $this->warungPkkRepository->delete($warungPkk);

        return redirect()->route('desa.warung-pkk.index')->with('success', 'Data warung PKK berhasil dihapus');
    }
}
