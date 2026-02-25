<?php

namespace App\Domains\Wilayah\DataKeluarga\Controllers;

use App\Domains\Wilayah\DataKeluarga\Actions\CreateScopedDataKeluargaAction;
use App\Domains\Wilayah\DataKeluarga\Actions\UpdateDataKeluargaAction;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;
use App\Domains\Wilayah\DataKeluarga\Requests\ListDataKeluargaRequest;
use App\Domains\Wilayah\DataKeluarga\Requests\StoreDataKeluargaRequest;
use App\Domains\Wilayah\DataKeluarga\Requests\UpdateDataKeluargaRequest;
use App\Domains\Wilayah\DataKeluarga\UseCases\GetScopedDataKeluargaUseCase;
use App\Domains\Wilayah\DataKeluarga\UseCases\ListScopedDataKeluargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataKeluargaController extends Controller
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository,
        private readonly ListScopedDataKeluargaUseCase $listScopedDataKeluargaUseCase,
        private readonly GetScopedDataKeluargaUseCase $getScopedDataKeluargaUseCase,
        private readonly CreateScopedDataKeluargaAction $createScopedDataKeluargaAction,
        private readonly UpdateDataKeluargaAction $updateDataKeluargaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListDataKeluargaRequest $request): Response
    {
        $this->authorize('viewAny', DataKeluarga::class);
        $items = $this->listScopedDataKeluargaUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (DataKeluarga $item) => [
                'id' => $item->id,
                'kategori_keluarga' => $item->kategori_keluarga,
                'jumlah_keluarga' => $item->jumlah_keluarga,
                'keterangan' => $item->keterangan,
            ]);

        return Inertia::render('Kecamatan/DataKeluarga/Index', [
            'dataKeluargaItems' => $items,
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
        $this->authorize('create', DataKeluarga::class);

        return Inertia::render('Kecamatan/DataKeluarga/Create', [
            'kategoriOptions' => DataKeluarga::kategoriOptions(),
        ]);
    }

    public function store(StoreDataKeluargaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataKeluarga::class);
        $this->createScopedDataKeluargaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.data-keluarga.index')->with('success', 'Data keluarga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataKeluarga);

        return Inertia::render('Kecamatan/DataKeluarga/Show', [
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
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataKeluarga);

        return Inertia::render('Kecamatan/DataKeluarga/Edit', [
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
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataKeluarga);
        $this->updateDataKeluargaAction->execute($dataKeluarga, $request->validated());

        return redirect()->route('kecamatan.data-keluarga.index')->with('success', 'Data keluarga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataKeluarga = $this->getScopedDataKeluargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataKeluarga);
        $this->dataKeluargaRepository->delete($dataKeluarga);

        return redirect()->route('kecamatan.data-keluarga.index')->with('success', 'Data keluarga berhasil dihapus');
    }
}
