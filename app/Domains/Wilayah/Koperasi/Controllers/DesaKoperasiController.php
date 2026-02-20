<?php

namespace App\Domains\Wilayah\Koperasi\Controllers;

use App\Domains\Wilayah\Koperasi\Actions\CreateScopedKoperasiAction;
use App\Domains\Wilayah\Koperasi\Actions\UpdateKoperasiAction;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;
use App\Domains\Wilayah\Koperasi\Requests\StoreKoperasiRequest;
use App\Domains\Wilayah\Koperasi\Requests\UpdateKoperasiRequest;
use App\Domains\Wilayah\Koperasi\UseCases\GetScopedKoperasiUseCase;
use App\Domains\Wilayah\Koperasi\UseCases\ListScopedKoperasiUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaKoperasiController extends Controller
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository,
        private readonly ListScopedKoperasiUseCase $listScopedKoperasiUseCase,
        private readonly GetScopedKoperasiUseCase $getScopedKoperasiUseCase,
        private readonly CreateScopedKoperasiAction $createScopedKoperasiAction,
        private readonly UpdateKoperasiAction $updateKoperasiAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Koperasi::class);
        $items = $this->listScopedKoperasiUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/Koperasi/Index', [
            'koperasiItems' => $items->values()->map(fn (Koperasi $item) => [
                'id' => $item->id,
                'nama_koperasi' => $item->nama_koperasi,
                'jenis_usaha' => $item->jenis_usaha,
                'berbadan_hukum' => $item->berbadan_hukum,
                'belum_berbadan_hukum' => $item->belum_berbadan_hukum,
                'jumlah_anggota_l' => $item->jumlah_anggota_l,
                'jumlah_anggota_p' => $item->jumlah_anggota_p,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Koperasi::class);

        return Inertia::render('Desa/Koperasi/Create');
    }

    public function store(StoreKoperasiRequest $request): RedirectResponse
    {
        $this->authorize('create', Koperasi::class);
        $this->createScopedKoperasiAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.koperasi.index')->with('success', 'Data koperasi berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $koperasi = $this->getScopedKoperasiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $koperasi);

        return Inertia::render('Desa/Koperasi/Show', [
            'koperasi' => [
                'id' => $koperasi->id,
                'nama_koperasi' => $koperasi->nama_koperasi,
                'jenis_usaha' => $koperasi->jenis_usaha,
                'berbadan_hukum' => $koperasi->berbadan_hukum,
                'belum_berbadan_hukum' => $koperasi->belum_berbadan_hukum,
                'jumlah_anggota_l' => $koperasi->jumlah_anggota_l,
                'jumlah_anggota_p' => $koperasi->jumlah_anggota_p,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $koperasi = $this->getScopedKoperasiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $koperasi);

        return Inertia::render('Desa/Koperasi/Edit', [
            'koperasi' => [
                'id' => $koperasi->id,
                'nama_koperasi' => $koperasi->nama_koperasi,
                'jenis_usaha' => $koperasi->jenis_usaha,
                'berbadan_hukum' => $koperasi->berbadan_hukum,
                'belum_berbadan_hukum' => $koperasi->belum_berbadan_hukum,
                'jumlah_anggota_l' => $koperasi->jumlah_anggota_l,
                'jumlah_anggota_p' => $koperasi->jumlah_anggota_p,
            ],
        ]);
    }

    public function update(UpdateKoperasiRequest $request, int $id): RedirectResponse
    {
        $koperasi = $this->getScopedKoperasiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $koperasi);
        $this->updateKoperasiAction->execute($koperasi, $request->validated());

        return redirect()->route('desa.koperasi.index')->with('success', 'Data koperasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $koperasi = $this->getScopedKoperasiUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $koperasi);
        $this->koperasiRepository->delete($koperasi);

        return redirect()->route('desa.koperasi.index')->with('success', 'Data koperasi berhasil dihapus');
    }
}


