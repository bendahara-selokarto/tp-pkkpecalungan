<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PraKoperasiUp2k\Actions\CreateScopedPraKoperasiUp2kAction;
use App\Domains\Wilayah\PraKoperasiUp2k\Actions\UpdatePraKoperasiUp2kAction;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use App\Domains\Wilayah\PraKoperasiUp2k\Repositories\PraKoperasiUp2kRepositoryInterface;
use App\Domains\Wilayah\PraKoperasiUp2k\Requests\ListPraKoperasiUp2kRequest;
use App\Domains\Wilayah\PraKoperasiUp2k\Requests\StorePraKoperasiUp2kRequest;
use App\Domains\Wilayah\PraKoperasiUp2k\Requests\UpdatePraKoperasiUp2kRequest;
use App\Domains\Wilayah\PraKoperasiUp2k\UseCases\GetScopedPraKoperasiUp2kUseCase;
use App\Domains\Wilayah\PraKoperasiUp2k\UseCases\ListScopedPraKoperasiUp2kUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaPraKoperasiUp2kController extends Controller
{
    public function __construct(
        private readonly PraKoperasiUp2kRepositoryInterface $praKoperasiUp2kRepository,
        private readonly ListScopedPraKoperasiUp2kUseCase $listScopedPraKoperasiUp2kUseCase,
        private readonly GetScopedPraKoperasiUp2kUseCase $getScopedPraKoperasiUp2kUseCase,
        private readonly CreateScopedPraKoperasiUp2kAction $createScopedPraKoperasiUp2kAction,
        private readonly UpdatePraKoperasiUp2kAction $updatePraKoperasiUp2kAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListPraKoperasiUp2kRequest $request): Response
    {
        $this->authorize('viewAny', PraKoperasiUp2k::class);
        $items = $this->listScopedPraKoperasiUp2kUseCase->execute(ScopeLevel::DESA->value, $request->perPage());

        return Inertia::render('Desa/PraKoperasiUp2k/Index', [
            'praKoperasiItems' => $items->through(fn (PraKoperasiUp2k $item) => [
                'id' => $item->id,
                'tingkat' => $item->tingkat,
                'jumlah_kelompok' => $item->jumlah_kelompok,
                'jumlah_peserta' => $item->jumlah_peserta,
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
        $this->authorize('create', PraKoperasiUp2k::class);

        return Inertia::render('Desa/PraKoperasiUp2k/Create');
    }

    public function store(StorePraKoperasiUp2kRequest $request): RedirectResponse
    {
        $this->authorize('create', PraKoperasiUp2k::class);
        $this->createScopedPraKoperasiUp2kAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.pra-koperasi-up2k.index')->with('success', 'Data pra koperasi/UP2K berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $praKoperasi = $this->getScopedPraKoperasiUp2kUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $praKoperasi);

        return Inertia::render('Desa/PraKoperasiUp2k/Show', [
            'praKoperasi' => [
                'id' => $praKoperasi->id,
                'tingkat' => $praKoperasi->tingkat,
                'jumlah_kelompok' => $praKoperasi->jumlah_kelompok,
                'jumlah_peserta' => $praKoperasi->jumlah_peserta,
                'keterangan' => $praKoperasi->keterangan,
                'tahun_anggaran' => $praKoperasi->tahun_anggaran,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $praKoperasi = $this->getScopedPraKoperasiUp2kUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $praKoperasi);

        return Inertia::render('Desa/PraKoperasiUp2k/Edit', [
            'praKoperasi' => [
                'id' => $praKoperasi->id,
                'tingkat' => $praKoperasi->tingkat,
                'jumlah_kelompok' => $praKoperasi->jumlah_kelompok,
                'jumlah_peserta' => $praKoperasi->jumlah_peserta,
                'keterangan' => $praKoperasi->keterangan,
                'tahun_anggaran' => $praKoperasi->tahun_anggaran,
            ],
        ]);
    }

    public function update(UpdatePraKoperasiUp2kRequest $request, int $id): RedirectResponse
    {
        $praKoperasi = $this->getScopedPraKoperasiUp2kUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $praKoperasi);
        $this->updatePraKoperasiUp2kAction->execute($praKoperasi, $request->validated());

        return redirect()->route('desa.pra-koperasi-up2k.index')->with('success', 'Data pra koperasi/UP2K berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $praKoperasi = $this->getScopedPraKoperasiUp2kUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $praKoperasi);
        $this->praKoperasiUp2kRepository->delete($praKoperasi);

        return redirect()->route('desa.pra-koperasi-up2k.index')->with('success', 'Data pra koperasi/UP2K berhasil dihapus');
    }
}
