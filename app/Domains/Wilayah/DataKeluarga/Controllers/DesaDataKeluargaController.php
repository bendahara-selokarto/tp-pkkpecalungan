<?php

namespace App\Domains\Wilayah\DataKeluarga\Controllers;

use App\Domains\Wilayah\DataKeluarga\Actions\CreateScopedDataKeluargaAction;
use App\Domains\Wilayah\DataKeluarga\Actions\UpdateDataKeluargaAction;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Requests\StoreDataKeluargaRequest;
use App\Domains\Wilayah\DataKeluarga\Requests\UpdateDataKeluargaRequest;
use App\Domains\Wilayah\DataKeluarga\UseCases\GetScopedDataKeluargaUseCase;
use App\Domains\Wilayah\DataKeluarga\UseCases\ListScopedDataKeluargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaDataKeluargaController extends Controller
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly ListScopedDataKeluargaUseCase $listScopedDataKeluargaUseCase,
        private readonly GetScopedDataKeluargaUseCase $getScopedDataKeluargaUseCase,
        private readonly CreateScopedDataKeluargaAction $createScopedDataKeluargaAction,
        private readonly UpdateDataKeluargaAction $updateDataKeluargaAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', DataKeluarga::class);
        $items = $this->listScopedDataKeluargaUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/DataKeluarga/Index', [
            'dataKeluargaItems' => $items->values()->map(fn (DataKeluarga $item) => [
                'id' => $item->id,
                'kategori_keluarga' => $item->kategori_keluarga,
                'jumlah_keluarga' => $item->jumlah_keluarga,
                'keterangan' => $item->keterangan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', DataKeluarga::class);

        return Inertia::render('Desa/DataKeluarga/Create', [
            'kategoriOptions' => DataKeluarga::kategoriOptions(),
        ]);
    }

    public function store(StoreDataKeluargaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataKeluarga::class);
        $this->createScopedDataKeluargaAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.data-keluarga.index')->with('success', 'Data keluarga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $dataKeluarga);

        return Inertia::render('Desa/DataKeluarga/Show', [
            'dataKeluarga' => [
                'id' => $dataKeluarga->id,
                'kategori_keluarga' => $dataKeluarga->kategori_keluarga,
                'jumlah_keluarga' => $dataKeluarga->jumlah_keluarga,
                'keterangan' => $dataKeluarga->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataKeluarga);

        return Inertia::render('Desa/DataKeluarga/Edit', [
            'dataKeluarga' => [
                'id' => $dataKeluarga->id,
                'kategori_keluarga' => $dataKeluarga->kategori_keluarga,
                'jumlah_keluarga' => $dataKeluarga->jumlah_keluarga,
                'keterangan' => $dataKeluarga->keterangan,
            ],
            'kategoriOptions' => DataKeluarga::kategoriOptions(),
        ]);
    }

    public function update(UpdateDataKeluargaRequest $request, int $id): RedirectResponse
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataKeluarga);
        $this->updateDataKeluargaAction->execute($dataKeluarga, $request->validated());

        return redirect()->route('desa.data-keluarga.index')->with('success', 'Data keluarga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $dataKeluarga);
        $this->dataKeluargaRepository->delete($dataKeluarga);

        return redirect()->route('desa.data-keluarga.index')->with('success', 'Data keluarga berhasil dihapus');
    }
}

