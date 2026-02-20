<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\SimulasiPenyuluhan\Actions\CreateScopedSimulasiPenyuluhanAction;
use App\Domains\Wilayah\SimulasiPenyuluhan\Actions\UpdateSimulasiPenyuluhanAction;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Domains\Wilayah\SimulasiPenyuluhan\Repositories\SimulasiPenyuluhanRepositoryInterface;
use App\Domains\Wilayah\SimulasiPenyuluhan\Requests\StoreSimulasiPenyuluhanRequest;
use App\Domains\Wilayah\SimulasiPenyuluhan\Requests\UpdateSimulasiPenyuluhanRequest;
use App\Domains\Wilayah\SimulasiPenyuluhan\UseCases\GetScopedSimulasiPenyuluhanUseCase;
use App\Domains\Wilayah\SimulasiPenyuluhan\UseCases\ListScopedSimulasiPenyuluhanUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanSimulasiPenyuluhanController extends Controller
{
    public function __construct(
        private readonly SimulasiPenyuluhanRepositoryInterface $simulasiPenyuluhanRepository,
        private readonly ListScopedSimulasiPenyuluhanUseCase $listScopedSimulasiPenyuluhanUseCase,
        private readonly GetScopedSimulasiPenyuluhanUseCase $getScopedSimulasiPenyuluhanUseCase,
        private readonly CreateScopedSimulasiPenyuluhanAction $createScopedSimulasiPenyuluhanAction,
        private readonly UpdateSimulasiPenyuluhanAction $updateSimulasiPenyuluhanAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', SimulasiPenyuluhan::class);
        $items = $this->listScopedSimulasiPenyuluhanUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/SimulasiPenyuluhan/Index', [
            'simulasiPenyuluhanItems' => $items->values()->map(fn (SimulasiPenyuluhan $item) => [
                'id' => $item->id,
                'nama_kegiatan' => $item->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $item->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $item->jumlah_kelompok,
                'jumlah_sosialisasi' => $item->jumlah_sosialisasi,
                'jumlah_kader_l' => $item->jumlah_kader_l,
                'jumlah_kader_p' => $item->jumlah_kader_p,
                'keterangan' => $item->keterangan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SimulasiPenyuluhan::class);

        return Inertia::render('Kecamatan/SimulasiPenyuluhan/Create');
    }

    public function store(StoreSimulasiPenyuluhanRequest $request): RedirectResponse
    {
        $this->authorize('create', SimulasiPenyuluhan::class);
        $this->createScopedSimulasiPenyuluhanAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $simulasiPenyuluhan);

        return Inertia::render('Kecamatan/SimulasiPenyuluhan/Show', [
            'simulasiPenyuluhan' => [
                'id' => $simulasiPenyuluhan->id,
                'nama_kegiatan' => $simulasiPenyuluhan->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $simulasiPenyuluhan->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $simulasiPenyuluhan->jumlah_kelompok,
                'jumlah_sosialisasi' => $simulasiPenyuluhan->jumlah_sosialisasi,
                'jumlah_kader_l' => $simulasiPenyuluhan->jumlah_kader_l,
                'jumlah_kader_p' => $simulasiPenyuluhan->jumlah_kader_p,
                'keterangan' => $simulasiPenyuluhan->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $simulasiPenyuluhan);

        return Inertia::render('Kecamatan/SimulasiPenyuluhan/Edit', [
            'simulasiPenyuluhan' => [
                'id' => $simulasiPenyuluhan->id,
                'nama_kegiatan' => $simulasiPenyuluhan->nama_kegiatan,
                'jenis_simulasi_penyuluhan' => $simulasiPenyuluhan->jenis_simulasi_penyuluhan,
                'jumlah_kelompok' => $simulasiPenyuluhan->jumlah_kelompok,
                'jumlah_sosialisasi' => $simulasiPenyuluhan->jumlah_sosialisasi,
                'jumlah_kader_l' => $simulasiPenyuluhan->jumlah_kader_l,
                'jumlah_kader_p' => $simulasiPenyuluhan->jumlah_kader_p,
                'keterangan' => $simulasiPenyuluhan->keterangan,
            ],
        ]);
    }

    public function update(UpdateSimulasiPenyuluhanRequest $request, int $id): RedirectResponse
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $simulasiPenyuluhan);
        $this->updateSimulasiPenyuluhanAction->execute($simulasiPenyuluhan, $request->validated());

        return redirect()->route('kecamatan.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $simulasiPenyuluhan = $this->getScopedSimulasiPenyuluhanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $simulasiPenyuluhan);
        $this->simulasiPenyuluhanRepository->delete($simulasiPenyuluhan);

        return redirect()->route('kecamatan.simulasi-penyuluhan.index')->with('success', 'Data isian kelompok simulasi dan penyuluhan berhasil dihapus');
    }
}


