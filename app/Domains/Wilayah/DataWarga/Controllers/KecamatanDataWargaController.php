<?php

namespace App\Domains\Wilayah\DataWarga\Controllers;

use App\Domains\Wilayah\DataWarga\Actions\CreateScopedDataWargaAction;
use App\Domains\Wilayah\DataWarga\Actions\UpdateDataWargaAction;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Requests\StoreDataWargaRequest;
use App\Domains\Wilayah\DataWarga\Requests\UpdateDataWargaRequest;
use App\Domains\Wilayah\DataWarga\UseCases\GetScopedDataWargaUseCase;
use App\Domains\Wilayah\DataWarga\UseCases\ListScopedDataWargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataWargaController extends Controller
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly ListScopedDataWargaUseCase $listScopedDataWargaUseCase,
        private readonly GetScopedDataWargaUseCase $getScopedDataWargaUseCase,
        private readonly CreateScopedDataWargaAction $createScopedDataWargaAction,
        private readonly UpdateDataWargaAction $updateDataWargaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', DataWarga::class);
        $items = $this->listScopedDataWargaUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/DataWarga/Index', [
            'dataWargaItems' => $items->values()->map(fn (DataWarga $item) => [
                'id' => $item->id,
                'dasawisma' => $item->dasawisma,
                'nama_kepala_keluarga' => $item->nama_kepala_keluarga,
                'alamat' => $item->alamat,
                'jumlah_warga_laki_laki' => $item->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $item->jumlah_warga_perempuan,
                'total_warga' => $item->total_warga,
                'keterangan' => $item->keterangan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', DataWarga::class);

        return Inertia::render('Kecamatan/DataWarga/Create');
    }

    public function store(StoreDataWargaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataWarga::class);
        $this->createScopedDataWargaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataWarga);

        return Inertia::render('Kecamatan/DataWarga/Show', [
            'dataWarga' => [
                'id' => $dataWarga->id,
                'dasawisma' => $dataWarga->dasawisma,
                'nama_kepala_keluarga' => $dataWarga->nama_kepala_keluarga,
                'alamat' => $dataWarga->alamat,
                'jumlah_warga_laki_laki' => $dataWarga->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $dataWarga->jumlah_warga_perempuan,
                'total_warga' => $dataWarga->total_warga,
                'keterangan' => $dataWarga->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataWarga);

        return Inertia::render('Kecamatan/DataWarga/Edit', [
            'dataWarga' => [
                'id' => $dataWarga->id,
                'dasawisma' => $dataWarga->dasawisma,
                'nama_kepala_keluarga' => $dataWarga->nama_kepala_keluarga,
                'alamat' => $dataWarga->alamat,
                'jumlah_warga_laki_laki' => $dataWarga->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $dataWarga->jumlah_warga_perempuan,
                'keterangan' => $dataWarga->keterangan,
            ],
        ]);
    }

    public function update(UpdateDataWargaRequest $request, int $id): RedirectResponse
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataWarga);
        $this->updateDataWargaAction->execute($dataWarga, $request->validated());

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataWarga);
        $this->dataWargaRepository->delete($dataWarga);

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil dihapus');
    }
}
