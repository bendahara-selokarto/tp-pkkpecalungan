<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Controllers;

use App\Domains\Wilayah\DataIndustriRumahTangga\Actions\CreateScopedDataIndustriRumahTanggaAction;
use App\Domains\Wilayah\DataIndustriRumahTangga\Actions\UpdateDataIndustriRumahTanggaAction;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Requests\StoreDataIndustriRumahTanggaRequest;
use App\Domains\Wilayah\DataIndustriRumahTangga\Requests\UpdateDataIndustriRumahTanggaRequest;
use App\Domains\Wilayah\DataIndustriRumahTangga\UseCases\GetScopedDataIndustriRumahTanggaUseCase;
use App\Domains\Wilayah\DataIndustriRumahTangga\UseCases\ListScopedDataIndustriRumahTanggaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataIndustriRumahTanggaController extends Controller
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly ListScopedDataIndustriRumahTanggaUseCase $listScopedDataIndustriRumahTanggaUseCase,
        private readonly GetScopedDataIndustriRumahTanggaUseCase $getScopedDataIndustriRumahTanggaUseCase,
        private readonly CreateScopedDataIndustriRumahTanggaAction $createScopedDataIndustriRumahTanggaAction,
        private readonly UpdateDataIndustriRumahTanggaAction $updateDataIndustriRumahTanggaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', DataIndustriRumahTangga::class);
        $items = $this->listScopedDataIndustriRumahTanggaUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/DataIndustriRumahTangga/Index', [
            'dataIndustriRumahTanggaItems' => $items->values()->map(fn (DataIndustriRumahTangga $item) => [
                'id' => $item->id,
                'kategori_jenis_industri' => $item->kategori_jenis_industri,
                'komoditi' => $item->komoditi,
                'jumlah_komoditi' => $item->jumlah_komoditi,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', DataIndustriRumahTangga::class);

        return Inertia::render('Kecamatan/DataIndustriRumahTangga/Create', [
            'kategoriJenisIndustriOptions' => DataIndustriRumahTangga::kategoriJenisIndustriOptions(),
        ]);
    }

    public function store(StoreDataIndustriRumahTanggaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataIndustriRumahTangga::class);
        $this->createScopedDataIndustriRumahTanggaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.data-industri-rumah-tangga.index')->with('success', 'Buku Industri Rumah Tangga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataIndustriRumahTangga = $this->getScopedDataIndustriRumahTanggaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataIndustriRumahTangga);

        return Inertia::render('Kecamatan/DataIndustriRumahTangga/Show', [
            'dataIndustriRumahTangga' => [
                'id' => $dataIndustriRumahTangga->id,
                'kategori_jenis_industri' => $dataIndustriRumahTangga->kategori_jenis_industri,
                'komoditi' => $dataIndustriRumahTangga->komoditi,
                'jumlah_komoditi' => $dataIndustriRumahTangga->jumlah_komoditi,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataIndustriRumahTangga = $this->getScopedDataIndustriRumahTanggaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataIndustriRumahTangga);

        return Inertia::render('Kecamatan/DataIndustriRumahTangga/Edit', [
            'dataIndustriRumahTangga' => [
                'id' => $dataIndustriRumahTangga->id,
                'kategori_jenis_industri' => $dataIndustriRumahTangga->kategori_jenis_industri,
                'komoditi' => $dataIndustriRumahTangga->komoditi,
                'jumlah_komoditi' => $dataIndustriRumahTangga->jumlah_komoditi,
            ],
            'kategoriJenisIndustriOptions' => DataIndustriRumahTangga::kategoriJenisIndustriOptions(),
        ]);
    }

    public function update(UpdateDataIndustriRumahTanggaRequest $request, int $id): RedirectResponse
    {
        $dataIndustriRumahTangga = $this->getScopedDataIndustriRumahTanggaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataIndustriRumahTangga);
        $this->updateDataIndustriRumahTanggaAction->execute($dataIndustriRumahTangga, $request->validated());

        return redirect()->route('kecamatan.data-industri-rumah-tangga.index')->with('success', 'Buku Industri Rumah Tangga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataIndustriRumahTangga = $this->getScopedDataIndustriRumahTanggaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataIndustriRumahTangga);
        $this->dataIndustriRumahTanggaRepository->delete($dataIndustriRumahTangga);

        return redirect()->route('kecamatan.data-industri-rumah-tangga.index')->with('success', 'Buku Industri Rumah Tangga berhasil dihapus');
    }
}


