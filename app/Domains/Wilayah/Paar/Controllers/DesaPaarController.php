<?php

namespace App\Domains\Wilayah\Paar\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Paar\Actions\CreateScopedPaarAction;
use App\Domains\Wilayah\Paar\Actions\UpdatePaarAction;
use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Paar\Repositories\PaarRepositoryInterface;
use App\Domains\Wilayah\Paar\Requests\ListPaarRequest;
use App\Domains\Wilayah\Paar\Requests\StorePaarRequest;
use App\Domains\Wilayah\Paar\Requests\UpdatePaarRequest;
use App\Domains\Wilayah\Paar\UseCases\GetScopedPaarUseCase;
use App\Domains\Wilayah\Paar\UseCases\ListScopedPaarUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaPaarController extends Controller
{
    public function __construct(
        private readonly PaarRepositoryInterface $paarRepository,
        private readonly ListScopedPaarUseCase $listScopedPaarUseCase,
        private readonly GetScopedPaarUseCase $getScopedPaarUseCase,
        private readonly CreateScopedPaarAction $createScopedPaarAction,
        private readonly UpdatePaarAction $updatePaarAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListPaarRequest $request): Response
    {
        $this->authorize('viewAny', Paar::class);

        $paarItems = $this->listScopedPaarUseCase
            ->execute(ScopeLevel::DESA->value, $request->perPage())
            ->through(fn (Paar $item): array => $this->buildItemPayload($item));

        return Inertia::render('Desa/Paar/Index', [
            'paarItems' => $paarItems,
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
        $this->authorize('create', Paar::class);

        return Inertia::render('Desa/Paar/Create', [
            'indicatorOptions' => Paar::indicatorOptions(),
        ]);
    }

    public function store(StorePaarRequest $request): RedirectResponse
    {
        $this->authorize('create', Paar::class);
        $this->createScopedPaarAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.paar.index')->with('success', 'Buku PAAR berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $paar = $this->getScopedPaarUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $paar);

        return Inertia::render('Desa/Paar/Show', [
            'paar' => $this->buildItemPayload($paar),
        ]);
    }

    public function edit(int $id): Response
    {
        $paar = $this->getScopedPaarUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $paar);

        return Inertia::render('Desa/Paar/Edit', [
            'paar' => $this->buildItemPayload($paar),
            'indicatorOptions' => Paar::indicatorOptions(),
        ]);
    }

    public function update(UpdatePaarRequest $request, int $id): RedirectResponse
    {
        $paar = $this->getScopedPaarUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $paar);
        $this->updatePaarAction->execute($paar, $request->validated());

        return redirect()->route('desa.paar.index')->with('success', 'Buku PAAR berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $paar = $this->getScopedPaarUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $paar);
        $this->paarRepository->delete($paar);

        return redirect()->route('desa.paar.index')->with('success', 'Buku PAAR berhasil dihapus');
    }

    /**
     * @return array{id:int,indikator:string,indikator_label:string,jumlah:int,keterangan:?string}
     */
    private function buildItemPayload(Paar $paar): array
    {
        return [
            'id' => (int) $paar->id,
            'indikator' => (string) $paar->indikator,
            'indikator_label' => Paar::indicatorLabel((string) $paar->indikator),
            'jumlah' => (int) $paar->jumlah,
            'keterangan' => $paar->keterangan,
        ];
    }
}
