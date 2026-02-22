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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaInventarisController extends Controller
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly ListScopedInventarisUseCase $listScopedInventarisUseCase,
        private readonly GetScopedInventarisUseCase $getScopedInventarisUseCase,
        private readonly CreateScopedInventarisAction $createScopedInventarisAction,
        private readonly UpdateInventarisAction $updateInventarisAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Inventaris::class);
        $inventaris = $this->listScopedInventarisUseCase->execute('desa');

        return Inertia::render('Desa/Inventaris/Index', [
            'inventaris' => $inventaris->values()->map(fn (Inventaris $item) => [
                'id' => $item->id,
                'name' => $item->name,
                'asal_barang' => $item->asal_barang,
                'description' => $item->description,
                'keterangan' => $item->keterangan ?? $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'tanggal_penerimaan' => $this->formatDateForPayload($item->tanggal_penerimaan),
                'tempat_penyimpanan' => $item->tempat_penyimpanan,
                'condition' => $item->condition,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Inventaris::class);

        return Inertia::render('Desa/Inventaris/Create');
    }

    public function store(StoreInventarisRequest $request): RedirectResponse
    {
        $this->authorize('create', Inventaris::class);
        $this->createScopedInventarisAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.inventaris.index')->with('success', 'Inventaris berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('view', $inventaris);

        return Inertia::render('Desa/Inventaris/Show', [
            'inventaris' => [
                'id' => $inventaris->id,
                'name' => $inventaris->name,
                'asal_barang' => $inventaris->asal_barang,
                'description' => $inventaris->description,
                'keterangan' => $inventaris->keterangan ?? $inventaris->description,
                'quantity' => $inventaris->quantity,
                'unit' => $inventaris->unit,
                'tanggal_penerimaan' => $this->formatDateForPayload($inventaris->tanggal_penerimaan),
                'tempat_penyimpanan' => $inventaris->tempat_penyimpanan,
                'condition' => $inventaris->condition,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('update', $inventaris);

        return Inertia::render('Desa/Inventaris/Edit', [
            'inventaris' => [
                'id' => $inventaris->id,
                'name' => $inventaris->name,
                'asal_barang' => $inventaris->asal_barang,
                'description' => $inventaris->description,
                'keterangan' => $inventaris->keterangan ?? $inventaris->description,
                'quantity' => $inventaris->quantity,
                'unit' => $inventaris->unit,
                'tanggal_penerimaan' => $this->formatDateForPayload($inventaris->tanggal_penerimaan),
                'tempat_penyimpanan' => $inventaris->tempat_penyimpanan,
                'condition' => $inventaris->condition,
            ],
        ]);
    }

    public function update(UpdateInventarisRequest $request, int $id): RedirectResponse
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('update', $inventaris);
        $this->updateInventarisAction->execute($inventaris, $request->validated());

        return redirect()->route('desa.inventaris.index')->with('success', 'Inventaris berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $inventaris = $this->getScopedInventarisUseCase->execute($id, 'desa');
        $this->authorize('delete', $inventaris);
        $this->inventarisRepository->delete($inventaris);

        return redirect()->route('desa.inventaris.index')->with('success', 'Inventaris berhasil dihapus');
    }

    private function formatDateForPayload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }
}
