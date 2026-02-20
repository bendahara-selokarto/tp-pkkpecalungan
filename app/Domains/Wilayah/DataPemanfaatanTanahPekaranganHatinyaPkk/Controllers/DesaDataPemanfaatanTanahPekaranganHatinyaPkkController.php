<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Controllers;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions\CreateScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions\UpdateDataPemanfaatanTanahPekaranganHatinyaPkkAction;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests\StoreDataPemanfaatanTanahPekaranganHatinyaPkkRequest;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests\UpdateDataPemanfaatanTanahPekaranganHatinyaPkkRequest;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases\GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\UseCases\ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaDataPemanfaatanTanahPekaranganHatinyaPkkController extends Controller
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly ListScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase $listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase,
        private readonly GetScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase $getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase,
        private readonly CreateScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction $createScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction,
        private readonly UpdateDataPemanfaatanTanahPekaranganHatinyaPkkAction $updateDataPemanfaatanTanahPekaranganHatinyaPkkAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', DataPemanfaatanTanahPekaranganHatinyaPkk::class);
        $items = $this->listScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Index', [
            'dataPemanfaatanTanahPekaranganHatinyaPkkItems' => $items->values()->map(fn (DataPemanfaatanTanahPekaranganHatinyaPkk $item) => [
                'id' => $item->id,
                'kategori_pemanfaatan_lahan' => $item->kategori_pemanfaatan_lahan,
                'komoditi' => $item->komoditi,
                'jumlah_komoditi' => $item->jumlah_komoditi,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', DataPemanfaatanTanahPekaranganHatinyaPkk::class);

        return Inertia::render('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Create', [
            'kategoriPemanfaatanLahanOptions' => DataPemanfaatanTanahPekaranganHatinyaPkk::kategoriPemanfaatanLahanOptions(),
        ]);
    }

    public function store(StoreDataPemanfaatanTanahPekaranganHatinyaPkkRequest $request): RedirectResponse
    {
        $this->authorize('create', DataPemanfaatanTanahPekaranganHatinyaPkk::class);
        $this->createScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.index')->with('success', 'Data Pemanfaatan Tanah Pekarangan/HATINYA PKK berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $dataPemanfaatanTanahPekaranganHatinyaPkk);

        return Inertia::render('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Show', [
            'dataPemanfaatanTanahPekaranganHatinyaPkk' => [
                'id' => $dataPemanfaatanTanahPekaranganHatinyaPkk->id,
                'kategori_pemanfaatan_lahan' => $dataPemanfaatanTanahPekaranganHatinyaPkk->kategori_pemanfaatan_lahan,
                'komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->komoditi,
                'jumlah_komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->jumlah_komoditi,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataPemanfaatanTanahPekaranganHatinyaPkk);

        return Inertia::render('Desa/DataPemanfaatanTanahPekaranganHatinyaPkk/Edit', [
            'dataPemanfaatanTanahPekaranganHatinyaPkk' => [
                'id' => $dataPemanfaatanTanahPekaranganHatinyaPkk->id,
                'kategori_pemanfaatan_lahan' => $dataPemanfaatanTanahPekaranganHatinyaPkk->kategori_pemanfaatan_lahan,
                'komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->komoditi,
                'jumlah_komoditi' => $dataPemanfaatanTanahPekaranganHatinyaPkk->jumlah_komoditi,
            ],
            'kategoriPemanfaatanLahanOptions' => DataPemanfaatanTanahPekaranganHatinyaPkk::kategoriPemanfaatanLahanOptions(),
        ]);
    }

    public function update(UpdateDataPemanfaatanTanahPekaranganHatinyaPkkRequest $request, int $id): RedirectResponse
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $this->updateDataPemanfaatanTanahPekaranganHatinyaPkkAction->execute($dataPemanfaatanTanahPekaranganHatinyaPkk, $request->validated());

        return redirect()->route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.index')->with('success', 'Data Pemanfaatan Tanah Pekarangan/HATINYA PKK berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataPemanfaatanTanahPekaranganHatinyaPkk = $this->getScopedDataPemanfaatanTanahPekaranganHatinyaPkkUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $dataPemanfaatanTanahPekaranganHatinyaPkk);
        $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->delete($dataPemanfaatanTanahPekaranganHatinyaPkk);

        return redirect()->route('desa.data-pemanfaatan-tanah-pekarangan-hatinya-pkk.index')->with('success', 'Data Pemanfaatan Tanah Pekarangan/HATINYA PKK berhasil dihapus');
    }
}
