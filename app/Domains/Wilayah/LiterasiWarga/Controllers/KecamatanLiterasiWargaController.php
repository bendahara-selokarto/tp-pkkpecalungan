<?php

namespace App\Domains\Wilayah\LiterasiWarga\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\LiterasiWarga\Actions\CreateScopedLiterasiWargaAction;
use App\Domains\Wilayah\LiterasiWarga\Actions\UpdateLiterasiWargaAction;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use App\Domains\Wilayah\LiterasiWarga\Repositories\LiterasiWargaRepositoryInterface;
use App\Domains\Wilayah\LiterasiWarga\Requests\ListLiterasiWargaRequest;
use App\Domains\Wilayah\LiterasiWarga\Requests\StoreLiterasiWargaRequest;
use App\Domains\Wilayah\LiterasiWarga\Requests\UpdateLiterasiWargaRequest;
use App\Domains\Wilayah\LiterasiWarga\UseCases\GetScopedLiterasiWargaUseCase;
use App\Domains\Wilayah\LiterasiWarga\UseCases\ListScopedLiterasiWargaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanLiterasiWargaController extends Controller
{
    public function __construct(
        private readonly LiterasiWargaRepositoryInterface $literasiWargaRepository,
        private readonly ListScopedLiterasiWargaUseCase $listScopedLiterasiWargaUseCase,
        private readonly GetScopedLiterasiWargaUseCase $getScopedLiterasiWargaUseCase,
        private readonly CreateScopedLiterasiWargaAction $createScopedLiterasiWargaAction,
        private readonly UpdateLiterasiWargaAction $updateLiterasiWargaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListLiterasiWargaRequest $request): Response
    {
        $this->authorize('viewAny', LiterasiWarga::class);
        $items = $this->listScopedLiterasiWargaUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage());

        return Inertia::render('Kecamatan/LiterasiWarga/Index', [
            'literasiWargaItems' => $items->through(fn (LiterasiWarga $item) => [
                'id' => $item->id,
                'jumlah_tiga_buta' => $item->jumlah_tiga_buta,
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
        $this->authorize('create', LiterasiWarga::class);

        return Inertia::render('Kecamatan/LiterasiWarga/Create');
    }

    public function store(StoreLiterasiWargaRequest $request): RedirectResponse
    {
        $this->authorize('create', LiterasiWarga::class);
        $this->createScopedLiterasiWargaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.literasi-warga.index')->with('success', 'Data literasi warga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $literasiWarga = $this->getScopedLiterasiWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $literasiWarga);

        return Inertia::render('Kecamatan/LiterasiWarga/Show', [
            'literasiWarga' => [
                'id' => $literasiWarga->id,
                'jumlah_tiga_buta' => $literasiWarga->jumlah_tiga_buta,
                'keterangan' => $literasiWarga->keterangan,
                'tahun_anggaran' => $literasiWarga->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $literasiWarga = $this->getScopedLiterasiWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $literasiWarga);

        return Inertia::render('Kecamatan/LiterasiWarga/Edit', [
            'literasiWarga' => [
                'id' => $literasiWarga->id,
                'jumlah_tiga_buta' => $literasiWarga->jumlah_tiga_buta,
                'keterangan' => $literasiWarga->keterangan,
                'tahun_anggaran' => $literasiWarga->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdateLiterasiWargaRequest $request, int $id): RedirectResponse
    {
        $literasiWarga = $this->getScopedLiterasiWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $literasiWarga);
        $this->updateLiterasiWargaAction->execute($literasiWarga, $request->validated());

        return redirect()->route('kecamatan.literasi-warga.index')->with('success', 'Data literasi warga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $literasiWarga = $this->getScopedLiterasiWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $literasiWarga);
        $this->literasiWargaRepository->delete($literasiWarga);

        return redirect()->route('kecamatan.literasi-warga.index')->with('success', 'Data literasi warga berhasil dihapus');
    }
}
