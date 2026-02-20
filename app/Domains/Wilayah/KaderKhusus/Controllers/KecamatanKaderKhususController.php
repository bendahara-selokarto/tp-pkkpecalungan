<?php

namespace App\Domains\Wilayah\KaderKhusus\Controllers;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\KaderKhusus\Actions\CreateScopedKaderKhususAction;
use App\Domains\Wilayah\KaderKhusus\Actions\UpdateKaderKhususAction;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Requests\StoreKaderKhususRequest;
use App\Domains\Wilayah\KaderKhusus\Requests\UpdateKaderKhususRequest;
use App\Domains\Wilayah\KaderKhusus\UseCases\GetScopedKaderKhususUseCase;
use App\Domains\Wilayah\KaderKhusus\UseCases\ListScopedKaderKhususUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanKaderKhususController extends Controller
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly ListScopedKaderKhususUseCase $listScopedKaderKhususUseCase,
        private readonly GetScopedKaderKhususUseCase $getScopedKaderKhususUseCase,
        private readonly CreateScopedKaderKhususAction $createScopedKaderKhususAction,
        private readonly UpdateKaderKhususAction $updateKaderKhususAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', KaderKhusus::class);
        $items = $this->listScopedKaderKhususUseCase->execute(ScopeLevel::KECAMATAN->value);

        return Inertia::render('Kecamatan/KaderKhusus/Index', [
            'kaderKhususItems' => $items->values()->map(fn (KaderKhusus $item) => [
                'id' => $item->id,
                'nama' => $item->nama,
                'jenis_kelamin' => $item->jenis_kelamin,
                'umur' => $item->umur,
                'status_perkawinan' => $item->status_perkawinan,
                'jenis_kader_khusus' => $item->jenis_kader_khusus,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', KaderKhusus::class);

        return Inertia::render('Kecamatan/KaderKhusus/Create');
    }

    public function store(StoreKaderKhususRequest $request): RedirectResponse
    {
        $this->authorize('create', KaderKhusus::class);
        $this->createScopedKaderKhususAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.kader-khusus.index')->with('success', 'Data kader khusus berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $kaderKhusus = $this->getScopedKaderKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $kaderKhusus);

        return Inertia::render('Kecamatan/KaderKhusus/Show', [
            'kaderKhusus' => [
                'id' => $kaderKhusus->id,
                'nama' => $kaderKhusus->nama,
                'jenis_kelamin' => $kaderKhusus->jenis_kelamin,
                'tempat_lahir' => $kaderKhusus->tempat_lahir,
                'tanggal_lahir' => optional($kaderKhusus->tanggal_lahir)->format('Y-m-d'),
                'umur' => $kaderKhusus->umur,
                'status_perkawinan' => $kaderKhusus->status_perkawinan,
                'alamat' => $kaderKhusus->alamat,
                'pendidikan' => $kaderKhusus->pendidikan,
                'jenis_kader_khusus' => $kaderKhusus->jenis_kader_khusus,
                'keterangan' => $kaderKhusus->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $kaderKhusus = $this->getScopedKaderKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $kaderKhusus);

        return Inertia::render('Kecamatan/KaderKhusus/Edit', [
            'kaderKhusus' => [
                'id' => $kaderKhusus->id,
                'nama' => $kaderKhusus->nama,
                'jenis_kelamin' => $kaderKhusus->jenis_kelamin,
                'tempat_lahir' => $kaderKhusus->tempat_lahir,
                'tanggal_lahir' => optional($kaderKhusus->tanggal_lahir)->format('d/m/Y'),
                'status_perkawinan' => $kaderKhusus->status_perkawinan,
                'alamat' => $kaderKhusus->alamat,
                'pendidikan' => $kaderKhusus->pendidikan,
                'jenis_kader_khusus' => $kaderKhusus->jenis_kader_khusus,
                'keterangan' => $kaderKhusus->keterangan,
            ],
        ]);
    }

    public function update(UpdateKaderKhususRequest $request, int $id): RedirectResponse
    {
        $kaderKhusus = $this->getScopedKaderKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $kaderKhusus);
        $this->updateKaderKhususAction->execute($kaderKhusus, $request->validated());

        return redirect()->route('kecamatan.kader-khusus.index')->with('success', 'Data kader khusus berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kaderKhusus = $this->getScopedKaderKhususUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $kaderKhusus);
        $this->kaderKhususRepository->delete($kaderKhusus);

        return redirect()->route('kecamatan.kader-khusus.index')->with('success', 'Data kader khusus berhasil dihapus');
    }
}
