<?php

namespace App\Domains\Wilayah\Posyandu\Controllers;

use App\Domains\Wilayah\Posyandu\Actions\CreateScopedPosyanduAction;
use App\Domains\Wilayah\Posyandu\Actions\UpdatePosyanduAction;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Posyandu\Repositories\PosyanduRepositoryInterface;
use App\Domains\Wilayah\Posyandu\Requests\StorePosyanduRequest;
use App\Domains\Wilayah\Posyandu\Requests\UpdatePosyanduRequest;
use App\Domains\Wilayah\Posyandu\UseCases\GetScopedPosyanduUseCase;
use App\Domains\Wilayah\Posyandu\UseCases\ListScopedPosyanduUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanPosyanduController extends Controller
{
    public function __construct(
        private readonly PosyanduRepositoryInterface $posyanduRepository,
        private readonly ListScopedPosyanduUseCase $listScopedPosyanduUseCase,
        private readonly GetScopedPosyanduUseCase $getScopedPosyanduUseCase,
        private readonly CreateScopedPosyanduAction $createScopedPosyanduAction,
        private readonly UpdatePosyanduAction $updatePosyanduAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Posyandu::class);
        $items = $this->listScopedPosyanduUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/Posyandu/Index', [
            'posyanduItems' => $items->values()->map(fn (Posyandu $item) => [
                'id' => $item->id,
                'nama_posyandu' => $item->nama_posyandu,
                'nama_pengelola' => $item->nama_pengelola,
                'nama_sekretaris' => $item->nama_sekretaris,
                'jenis_posyandu' => $item->jenis_posyandu,
                'jumlah_kader' => $item->jumlah_kader,
                'jenis_kegiatan' => $item->jenis_kegiatan,
                'frekuensi_layanan' => $item->frekuensi_layanan,
                'jumlah_pengunjung_l' => $item->jumlah_pengunjung_l,
                'jumlah_pengunjung_p' => $item->jumlah_pengunjung_p,
                'jumlah_petugas_l' => $item->jumlah_petugas_l,
                'jumlah_petugas_p' => $item->jumlah_petugas_p,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Posyandu::class);

        return Inertia::render('Kecamatan/Posyandu/Create');
    }

    public function store(StorePosyanduRequest $request): RedirectResponse
    {
        $this->authorize('create', Posyandu::class);
        $this->createScopedPosyanduAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.posyandu.index')->with('success', 'Data posyandu berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $posyandu = $this->getScopedPosyanduUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $posyandu);

        return Inertia::render('Kecamatan/Posyandu/Show', [
            'posyandu' => [
                'id' => $posyandu->id,
                'nama_posyandu' => $posyandu->nama_posyandu,
                'nama_pengelola' => $posyandu->nama_pengelola,
                'nama_sekretaris' => $posyandu->nama_sekretaris,
                'jenis_posyandu' => $posyandu->jenis_posyandu,
                'jumlah_kader' => $posyandu->jumlah_kader,
                'jenis_kegiatan' => $posyandu->jenis_kegiatan,
                'frekuensi_layanan' => $posyandu->frekuensi_layanan,
                'jumlah_pengunjung_l' => $posyandu->jumlah_pengunjung_l,
                'jumlah_pengunjung_p' => $posyandu->jumlah_pengunjung_p,
                'jumlah_petugas_l' => $posyandu->jumlah_petugas_l,
                'jumlah_petugas_p' => $posyandu->jumlah_petugas_p,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $posyandu = $this->getScopedPosyanduUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $posyandu);

        return Inertia::render('Kecamatan/Posyandu/Edit', [
            'posyandu' => [
                'id' => $posyandu->id,
                'nama_posyandu' => $posyandu->nama_posyandu,
                'nama_pengelola' => $posyandu->nama_pengelola,
                'nama_sekretaris' => $posyandu->nama_sekretaris,
                'jenis_posyandu' => $posyandu->jenis_posyandu,
                'jumlah_kader' => $posyandu->jumlah_kader,
                'jenis_kegiatan' => $posyandu->jenis_kegiatan,
                'frekuensi_layanan' => $posyandu->frekuensi_layanan,
                'jumlah_pengunjung_l' => $posyandu->jumlah_pengunjung_l,
                'jumlah_pengunjung_p' => $posyandu->jumlah_pengunjung_p,
                'jumlah_petugas_l' => $posyandu->jumlah_petugas_l,
                'jumlah_petugas_p' => $posyandu->jumlah_petugas_p,
            ],
        ]);
    }

    public function update(UpdatePosyanduRequest $request, int $id): RedirectResponse
    {
        $posyandu = $this->getScopedPosyanduUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $posyandu);
        $this->updatePosyanduAction->execute($posyandu, $request->validated());

        return redirect()->route('kecamatan.posyandu.index')->with('success', 'Data posyandu berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $posyandu = $this->getScopedPosyanduUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $posyandu);
        $this->posyanduRepository->delete($posyandu);

        return redirect()->route('kecamatan.posyandu.index')->with('success', 'Data posyandu berhasil dihapus');
    }
}





