<?php

namespace App\Domains\Wilayah\PrestasiLomba\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\PrestasiLomba\Actions\CreateScopedPrestasiLombaAction;
use App\Domains\Wilayah\PrestasiLomba\Actions\UpdatePrestasiLombaAction;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;
use App\Domains\Wilayah\PrestasiLomba\Requests\StorePrestasiLombaRequest;
use App\Domains\Wilayah\PrestasiLomba\Requests\UpdatePrestasiLombaRequest;
use App\Domains\Wilayah\PrestasiLomba\UseCases\GetScopedPrestasiLombaUseCase;
use App\Domains\Wilayah\PrestasiLomba\UseCases\ListScopedPrestasiLombaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanPrestasiLombaController extends Controller
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository,
        private readonly ListScopedPrestasiLombaUseCase $listScopedPrestasiLombaUseCase,
        private readonly GetScopedPrestasiLombaUseCase $getScopedPrestasiLombaUseCase,
        private readonly CreateScopedPrestasiLombaAction $createScopedPrestasiLombaAction,
        private readonly UpdatePrestasiLombaAction $updatePrestasiLombaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', PrestasiLomba::class);
        $items = $this->listScopedPrestasiLombaUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/PrestasiLomba/Index', [
            'prestasiLombaItems' => $items->values()->map(fn (PrestasiLomba $item) => [
                'id' => $item->id,
                'tahun' => $item->tahun,
                'jenis_lomba' => $item->jenis_lomba,
                'lokasi' => $item->lokasi,
                'prestasi_kecamatan' => (bool) $item->prestasi_kecamatan,
                'prestasi_kabupaten' => (bool) $item->prestasi_kabupaten,
                'prestasi_provinsi' => (bool) $item->prestasi_provinsi,
                'prestasi_nasional' => (bool) $item->prestasi_nasional,
                'keterangan' => $item->keterangan,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', PrestasiLomba::class);

        return Inertia::render('Kecamatan/PrestasiLomba/Create');
    }

    public function store(StorePrestasiLombaRequest $request): RedirectResponse
    {
        $this->authorize('create', PrestasiLomba::class);
        $this->createScopedPrestasiLombaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.prestasi-lomba.index')->with('success', 'Data buku prestasi berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $prestasiLomba = $this->getScopedPrestasiLombaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $prestasiLomba);

        return Inertia::render('Kecamatan/PrestasiLomba/Show', [
            'prestasiLomba' => [
                'id' => $prestasiLomba->id,
                'tahun' => $prestasiLomba->tahun,
                'jenis_lomba' => $prestasiLomba->jenis_lomba,
                'lokasi' => $prestasiLomba->lokasi,
                'prestasi_kecamatan' => (bool) $prestasiLomba->prestasi_kecamatan,
                'prestasi_kabupaten' => (bool) $prestasiLomba->prestasi_kabupaten,
                'prestasi_provinsi' => (bool) $prestasiLomba->prestasi_provinsi,
                'prestasi_nasional' => (bool) $prestasiLomba->prestasi_nasional,
                'keterangan' => $prestasiLomba->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $prestasiLomba = $this->getScopedPrestasiLombaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $prestasiLomba);

        return Inertia::render('Kecamatan/PrestasiLomba/Edit', [
            'prestasiLomba' => [
                'id' => $prestasiLomba->id,
                'tahun' => $prestasiLomba->tahun,
                'jenis_lomba' => $prestasiLomba->jenis_lomba,
                'lokasi' => $prestasiLomba->lokasi,
                'prestasi_kecamatan' => (bool) $prestasiLomba->prestasi_kecamatan,
                'prestasi_kabupaten' => (bool) $prestasiLomba->prestasi_kabupaten,
                'prestasi_provinsi' => (bool) $prestasiLomba->prestasi_provinsi,
                'prestasi_nasional' => (bool) $prestasiLomba->prestasi_nasional,
                'keterangan' => $prestasiLomba->keterangan,
            ],
        ]);
    }

    public function update(UpdatePrestasiLombaRequest $request, int $id): RedirectResponse
    {
        $prestasiLomba = $this->getScopedPrestasiLombaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $prestasiLomba);
        $this->updatePrestasiLombaAction->execute($prestasiLomba, $request->validated());

        return redirect()->route('kecamatan.prestasi-lomba.index')->with('success', 'Data buku prestasi berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $prestasiLomba = $this->getScopedPrestasiLombaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $prestasiLomba);
        $this->prestasiLombaRepository->delete($prestasiLomba);

        return redirect()->route('kecamatan.prestasi-lomba.index')->with('success', 'Data buku prestasi berhasil dihapus');
    }
}
