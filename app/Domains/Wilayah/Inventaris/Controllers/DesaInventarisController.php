<?php

namespace App\Domains\Wilayah\Inventaris\Controllers;

use App\Domains\Wilayah\Inventaris\Actions\CreateScopedInventarisAction;
use App\Domains\Wilayah\Inventaris\Actions\UpdateInventarisAction;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepository;
use App\Domains\Wilayah\Inventaris\Requests\StoreInventarisRequest;
use App\Domains\Wilayah\Inventaris\Requests\UpdateInventarisRequest;
use App\Domains\Wilayah\Inventaris\UseCases\GetScopedInventarisUseCase;
use App\Domains\Wilayah\Inventaris\UseCases\ListScopedInventarisUseCase;
use App\Http\Controllers\Controller;

class DesaInventarisController extends Controller
{
    public function __construct(
        private readonly InventarisRepository $inventarisRepository,
        private readonly ListScopedInventarisUseCase $listScopedInventarisUseCase,
        private readonly GetScopedInventarisUseCase $getScopedInventarisUseCase,
        private readonly CreateScopedInventarisAction $createScopedInventarisAction,
        private readonly UpdateInventarisAction $updateInventarisAction
    ) {
        $this->middleware('role:admin-desa');
    }

    public function index()
    {
        $this->authorize('viewAny', Inventaris::class);
        $inventaris = $this->listScopedInventarisUseCase->execute('desa');

        return view('desa.inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        $this->authorize('create', Inventaris::class);

        return view('desa.inventaris.create');
    }

    public function store(StoreInventarisRequest $request)
    {
        $this->authorize('create', Inventaris::class);
        $this->createScopedInventarisAction->execute($request->validated(), 'desa');

        return redirect('/desa/inventaris');
    }

    public function show(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('view', $inventaris);

        return view('desa.inventaris.show', compact('inventaris'));
    }

    public function edit(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('update', $inventaris);

        return view('desa.inventaris.edit', compact('inventaris'));
    }

    public function update(UpdateInventarisRequest $request, int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('update', $inventaris);
        $this->updateInventarisAction->execute($inventaris, $request->validated());

        return redirect('/desa/inventaris');
    }

    public function destroy(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('delete', $inventaris);
        $this->inventarisRepository->delete($inventaris);

        return redirect('/desa/inventaris');
    }
}
