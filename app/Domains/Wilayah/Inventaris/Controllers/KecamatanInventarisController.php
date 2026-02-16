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

class KecamatanInventarisController extends Controller
{
    public function __construct(
        private readonly InventarisRepository $inventarisRepository,
        private readonly ListScopedInventarisUseCase $listScopedInventarisUseCase,
        private readonly GetScopedInventarisUseCase $getScopedInventarisUseCase,
        private readonly CreateScopedInventarisAction $createScopedInventarisAction,
        private readonly UpdateInventarisAction $updateInventarisAction
    ) {
        $this->middleware('role:admin-kecamatan');
    }

    public function index()
    {
        $this->authorize('viewAny', Inventaris::class);
        $inventaris = $this->listScopedInventarisUseCase->execute('kecamatan');

        return view('kecamatan.inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        $this->authorize('create', Inventaris::class);

        return view('kecamatan.inventaris.create');
    }

    public function store(StoreInventarisRequest $request)
    {
        $this->authorize('create', Inventaris::class);
        $this->createScopedInventarisAction->execute($request->validated(), 'kecamatan');

        return redirect('/kecamatan/inventaris');
    }

    public function show(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('view', $inventaris);

        return view('kecamatan.inventaris.show', compact('inventaris'));
    }

    public function edit(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $inventaris);

        return view('kecamatan.inventaris.edit', compact('inventaris'));
    }

    public function update(UpdateInventarisRequest $request, int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('update', $inventaris);
        $this->updateInventarisAction->execute($inventaris, $request->validated());

        return redirect('/kecamatan/inventaris');
    }

    public function destroy(int $id)
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'kecamatan');
        $this->authorize('delete', $inventaris);
        $this->inventarisRepository->delete($inventaris);

        return redirect('/kecamatan/inventaris');
    }
}
