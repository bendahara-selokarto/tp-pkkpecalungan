<?php

namespace App\Domains\Wilayah\Inventaris\Controllers;

use App\Domains\Wilayah\Inventaris\Actions\CreateScopedInventarisAction;
use App\Domains\Wilayah\Inventaris\Actions\UpdateInventarisAction;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Requests\StoreInventarisRequest;
use App\Domains\Wilayah\Inventaris\Requests\UpdateInventarisRequest;
use App\Domains\Wilayah\Inventaris\UseCases\GetScopedInventarisUseCase;
use App\Domains\Wilayah\Inventaris\UseCases\ListScopedInventarisUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanInventarisController extends Controller
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly ListScopedInventarisUseCase $listScopedInventarisUseCase,
        private readonly GetScopedInventarisUseCase $getScopedInventarisUseCase,
        private readonly CreateScopedInventarisAction $createScopedInventarisAction,
        private readonly UpdateInventarisAction $updateInventarisAction
    ) {
        $this->middleware('role:admin-kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Inventaris::class);
        $inventaris = $this->listScopedInventarisUseCase->execute('kecamatan');

        return Inertia::render('Kecamatan/Inventaris/Index', [
            'inventaris' => $inventaris->values()->map(fn (Inventaris $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'condition' => $item->condition,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Inventaris::class);

        return Inertia::render('Kecamatan/Inventaris/Create');
    }

    public function store(StoreInventarisRequest $request): RedirectResponse
    {
        $this->authorize('create', Inventaris::class);
        $this->createScopedInventarisAction->execute($request->validated(), 'kecamatan');

        return redirect()->route('kecamatan.inventaris.index')->with('success', 'Inventaris berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $inventaris);

        return Inertia::render('Kecamatan/Inventaris/Show', [
            'inventaris' => [
                'id' => $inventaris->id,
                'name' => $inventaris->name,
                'description' => $inventaris->description,
                'quantity' => $inventaris->quantity,
                'unit' => $inventaris->unit,
                'condition' => $inventaris->condition,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $inventaris);

        return Inertia::render('Kecamatan/Inventaris/Edit', [
            'inventaris' => [
                'id' => $inventaris->id,
                'name' => $inventaris->name,
                'description' => $inventaris->description,
                'quantity' => $inventaris->quantity,
                'unit' => $inventaris->unit,
                'condition' => $inventaris->condition,
            ],
        ]);
    }

    public function update(UpdateInventarisRequest $request, int $id): RedirectResponse
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $inventaris);
        $this->updateInventarisAction->execute($inventaris, $request->validated());

        return redirect()->route('kecamatan.inventaris.index')->with('success', 'Inventaris berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $inventaris);
        $this->inventarisRepository->delete($inventaris);

        return redirect()->route('kecamatan.inventaris.index')->with('success', 'Inventaris berhasil dihapus');
    }
}

