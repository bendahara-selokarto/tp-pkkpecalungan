<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Controllers;

use App\Domains\Wilayah\DataKegiatanWarga\Actions\CreateScopedDataKegiatanWargaAction;
use App\Domains\Wilayah\DataKegiatanWarga\Actions\UpdateDataKegiatanWargaAction;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;
use App\Domains\Wilayah\DataKegiatanWarga\Requests\ListDataKegiatanWargaRequest;
use App\Domains\Wilayah\DataKegiatanWarga\Requests\StoreDataKegiatanWargaRequest;
use App\Domains\Wilayah\DataKegiatanWarga\Requests\UpdateDataKegiatanWargaRequest;
use App\Domains\Wilayah\DataKegiatanWarga\UseCases\GetScopedDataKegiatanWargaUseCase;
use App\Domains\Wilayah\DataKegiatanWarga\UseCases\ListScopedDataKegiatanWargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataKegiatanWargaController extends Controller
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository,
        private readonly ListScopedDataKegiatanWargaUseCase $listScopedDataKegiatanWargaUseCase,
        private readonly GetScopedDataKegiatanWargaUseCase $getScopedDataKegiatanWargaUseCase,
        private readonly CreateScopedDataKegiatanWargaAction $createScopedDataKegiatanWargaAction,
        private readonly UpdateDataKegiatanWargaAction $updateDataKegiatanWargaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListDataKegiatanWargaRequest $request): Response
    {
        $this->authorize('viewAny', DataKegiatanWarga::class);
        $items = $this->listScopedDataKegiatanWargaUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (DataKegiatanWarga $item) => [
                'id' => $item->id,
                'kegiatan' => $item->kegiatan,
                'aktivitas' => $item->aktivitas,
                'aktivitas_label' => $item->aktivitas ? 'Ya' : 'Tidak',
                'keterangan' => $item->keterangan,
            ]);

        return Inertia::render('Kecamatan/DataKegiatanWarga/Index', [
            'dataKegiatanWargaItems' => $items,
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
        $this->authorize('create', DataKegiatanWarga::class);

        return Inertia::render('Kecamatan/DataKegiatanWarga/Create', [
            'kegiatanOptions' => DataKegiatanWarga::kegiatanOptions(),
        ]);
    }

    public function store(StoreDataKegiatanWargaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataKegiatanWarga::class);
        $this->createScopedDataKegiatanWargaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.data-kegiatan-warga.index')->with('success', 'Data kegiatan warga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataKegiatanWarga = $this->getScopedDataKegiatanWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataKegiatanWarga);

        return Inertia::render('Kecamatan/DataKegiatanWarga/Show', [
            'dataKegiatanWarga' => [
                'id' => $dataKegiatanWarga->id,
                'kegiatan' => $dataKegiatanWarga->kegiatan,
                'aktivitas' => $dataKegiatanWarga->aktivitas,
                'aktivitas_label' => $dataKegiatanWarga->aktivitas ? 'Ya' : 'Tidak',
                'keterangan' => $dataKegiatanWarga->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataKegiatanWarga = $this->getScopedDataKegiatanWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataKegiatanWarga);

        return Inertia::render('Kecamatan/DataKegiatanWarga/Edit', [
            'dataKegiatanWarga' => [
                'id' => $dataKegiatanWarga->id,
                'kegiatan' => $dataKegiatanWarga->kegiatan,
                'aktivitas' => $dataKegiatanWarga->aktivitas,
                'keterangan' => $dataKegiatanWarga->keterangan,
            ],
            'kegiatanOptions' => DataKegiatanWarga::kegiatanOptions(),
        ]);
    }

    public function update(UpdateDataKegiatanWargaRequest $request, int $id): RedirectResponse
    {
        $dataKegiatanWarga = $this->getScopedDataKegiatanWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataKegiatanWarga);
        $this->updateDataKegiatanWargaAction->execute($dataKegiatanWarga, $request->validated());

        return redirect()->route('kecamatan.data-kegiatan-warga.index')->with('success', 'Data kegiatan warga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataKegiatanWarga = $this->getScopedDataKegiatanWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataKegiatanWarga);
        $this->dataKegiatanWargaRepository->delete($dataKegiatanWarga);

        return redirect()->route('kecamatan.data-kegiatan-warga.index')->with('success', 'Data kegiatan warga berhasil dihapus');
    }
}
