<?php

namespace App\Domains\Wilayah\BkbKegiatan\Controllers;

use App\Domains\Wilayah\BkbKegiatan\Actions\CreateScopedBkbKegiatanAction;
use App\Domains\Wilayah\BkbKegiatan\Actions\UpdateBkbKegiatanAction;
use App\Domains\Wilayah\BkbKegiatan\Models\BkbKegiatan;
use App\Domains\Wilayah\BkbKegiatan\Repositories\BkbKegiatanRepositoryInterface;
use App\Domains\Wilayah\BkbKegiatan\Requests\ListBkbKegiatanRequest;
use App\Domains\Wilayah\BkbKegiatan\Requests\StoreBkbKegiatanRequest;
use App\Domains\Wilayah\BkbKegiatan\Requests\UpdateBkbKegiatanRequest;
use App\Domains\Wilayah\BkbKegiatan\UseCases\GetScopedBkbKegiatanUseCase;
use App\Domains\Wilayah\BkbKegiatan\UseCases\ListScopedBkbKegiatanUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanBkbKegiatanController extends Controller
{
    public function __construct(
        private readonly BkbKegiatanRepositoryInterface $bkbKegiatanRepository,
        private readonly ListScopedBkbKegiatanUseCase $listScopedBkbKegiatanUseCase,
        private readonly GetScopedBkbKegiatanUseCase $getScopedBkbKegiatanUseCase,
        private readonly CreateScopedBkbKegiatanAction $createScopedBkbKegiatanAction,
        private readonly UpdateBkbKegiatanAction $updateBkbKegiatanAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListBkbKegiatanRequest $request): Response
    {
        $this->authorize('viewAny', BkbKegiatan::class);
        $items = $this->listScopedBkbKegiatanUseCase->execute(ScopeLevel::KECAMATAN->value, $request->perPage());

        return Inertia::render('Kecamatan/BkbKegiatan/Index', [
            'bkbKegiatanItems' => $items->through(fn (BkbKegiatan $item) => [
                'id' => $item->id,
                'jumlah_kelompok' => $item->jumlah_kelompok,
                'jumlah_ibu_peserta' => $item->jumlah_ibu_peserta,
                'jumlah_ape_set' => $item->jumlah_ape_set,
                'jumlah_kelompok_simulasi' => $item->jumlah_kelompok_simulasi,
                'keterangan' => $item->keterangan,
                'tahun_anggaran' => $item->tahun_anggaran,
            ]),
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
                'tahun_anggaran' => (int) $request->user()->active_budget_year,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BkbKegiatan::class);

        return Inertia::render('Kecamatan/BkbKegiatan/Create');
    }

    public function store(StoreBkbKegiatanRequest $request): RedirectResponse
    {
        $this->authorize('create', BkbKegiatan::class);
        $this->createScopedBkbKegiatanAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.bkb-kegiatan.index')->with('success', 'Data BKB berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $bkbKegiatan = $this->getScopedBkbKegiatanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $bkbKegiatan);

        return Inertia::render('Kecamatan/BkbKegiatan/Show', [
            'bkbKegiatan' => [
                'id' => $bkbKegiatan->id,
                'jumlah_kelompok' => $bkbKegiatan->jumlah_kelompok,
                'jumlah_ibu_peserta' => $bkbKegiatan->jumlah_ibu_peserta,
                'jumlah_ape_set' => $bkbKegiatan->jumlah_ape_set,
                'jumlah_kelompok_simulasi' => $bkbKegiatan->jumlah_kelompok_simulasi,
                'keterangan' => $bkbKegiatan->keterangan,
                'tahun_anggaran' => $bkbKegiatan->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $bkbKegiatan = $this->getScopedBkbKegiatanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkbKegiatan);

        return Inertia::render('Kecamatan/BkbKegiatan/Edit', [
            'bkbKegiatan' => [
                'id' => $bkbKegiatan->id,
                'jumlah_kelompok' => $bkbKegiatan->jumlah_kelompok,
                'jumlah_ibu_peserta' => $bkbKegiatan->jumlah_ibu_peserta,
                'jumlah_ape_set' => $bkbKegiatan->jumlah_ape_set,
                'jumlah_kelompok_simulasi' => $bkbKegiatan->jumlah_kelompok_simulasi,
                'keterangan' => $bkbKegiatan->keterangan,
                'tahun_anggaran' => $bkbKegiatan->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateBkbKegiatanRequest $request, int $id): RedirectResponse
    {
        $bkbKegiatan = $this->getScopedBkbKegiatanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $bkbKegiatan);
        $this->updateBkbKegiatanAction->execute($bkbKegiatan, $request->validated());

        return redirect()->route('kecamatan.bkb-kegiatan.index')->with('success', 'Data BKB berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bkbKegiatan = $this->getScopedBkbKegiatanUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $bkbKegiatan);
        $this->bkbKegiatanRepository->delete($bkbKegiatan);

        return redirect()->route('kecamatan.bkb-kegiatan.index')->with('success', 'Data BKB berhasil dihapus');
    }
}
