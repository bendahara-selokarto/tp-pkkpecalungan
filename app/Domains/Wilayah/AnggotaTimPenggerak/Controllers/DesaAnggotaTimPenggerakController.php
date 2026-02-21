<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Controllers;

use App\Domains\Wilayah\AnggotaTimPenggerak\Actions\CreateScopedAnggotaTimPenggerakAction;
use App\Domains\Wilayah\AnggotaTimPenggerak\Actions\UpdateAnggotaTimPenggerakAction;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Requests\StoreAnggotaTimPenggerakRequest;
use App\Domains\Wilayah\AnggotaTimPenggerak\Requests\UpdateAnggotaTimPenggerakRequest;
use App\Domains\Wilayah\AnggotaTimPenggerak\UseCases\GetScopedAnggotaTimPenggerakUseCase;
use App\Domains\Wilayah\AnggotaTimPenggerak\UseCases\ListScopedAnggotaTimPenggerakUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaAnggotaTimPenggerakController extends Controller
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly ListScopedAnggotaTimPenggerakUseCase $listScopedAnggotaTimPenggerakUseCase,
        private readonly GetScopedAnggotaTimPenggerakUseCase $getScopedAnggotaTimPenggerakUseCase,
        private readonly CreateScopedAnggotaTimPenggerakAction $createScopedAnggotaTimPenggerakAction,
        private readonly UpdateAnggotaTimPenggerakAction $updateAnggotaTimPenggerakAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', AnggotaTimPenggerak::class);
        $anggotaTimPenggeraks = $this->listScopedAnggotaTimPenggerakUseCase->execute('desa');

        return Inertia::render('Desa/AnggotaTimPenggerak/Index', [
            'anggotaTimPenggeraks' => $anggotaTimPenggeraks->values()->map(fn (AnggotaTimPenggerak $item) => [
                'id' => $item->id,
                'nama' => $item->nama,
                'jabatan' => $item->jabatan,
                'jenis_kelamin' => $item->jenis_kelamin,
                'umur' => $item->umur,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', AnggotaTimPenggerak::class);

        return Inertia::render('Desa/AnggotaTimPenggerak/Create');
    }

    public function store(StoreAnggotaTimPenggerakRequest $request): RedirectResponse
    {
        $this->authorize('create', AnggotaTimPenggerak::class);
        $this->createScopedAnggotaTimPenggerakAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.anggota-tim-penggerak.index')->with('success', 'Data anggota tim penggerak berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $anggotaTimPenggerak = $this->getScopedAnggotaTimPenggerakUseCase->execute($id, 'desa');
        $this->authorize('view', $anggotaTimPenggerak);

        return Inertia::render('Desa/AnggotaTimPenggerak/Show', [
            'anggotaTimPenggerak' => [
                'id' => $anggotaTimPenggerak->id,
                'nama' => $anggotaTimPenggerak->nama,
                'jabatan' => $anggotaTimPenggerak->jabatan,
                'jenis_kelamin' => $anggotaTimPenggerak->jenis_kelamin,
                'tempat_lahir' => $anggotaTimPenggerak->tempat_lahir,
                'tanggal_lahir' => optional($anggotaTimPenggerak->tanggal_lahir)->format('Y-m-d'),
                'umur' => $anggotaTimPenggerak->umur,
                'status_perkawinan' => $anggotaTimPenggerak->status_perkawinan,
                'alamat' => $anggotaTimPenggerak->alamat,
                'pendidikan' => $anggotaTimPenggerak->pendidikan,
                'pekerjaan' => $anggotaTimPenggerak->pekerjaan,
                'keterangan' => $anggotaTimPenggerak->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $anggotaTimPenggerak = $this->getScopedAnggotaTimPenggerakUseCase->execute($id, 'desa');
        $this->authorize('update', $anggotaTimPenggerak);

        return Inertia::render('Desa/AnggotaTimPenggerak/Edit', [
            'anggotaTimPenggerak' => [
                'id' => $anggotaTimPenggerak->id,
                'nama' => $anggotaTimPenggerak->nama,
                'jabatan' => $anggotaTimPenggerak->jabatan,
                'jenis_kelamin' => $anggotaTimPenggerak->jenis_kelamin,
                'tempat_lahir' => $anggotaTimPenggerak->tempat_lahir,
                'tanggal_lahir' => optional($anggotaTimPenggerak->tanggal_lahir)->format('Y-m-d'),
                'status_perkawinan' => $anggotaTimPenggerak->status_perkawinan,
                'alamat' => $anggotaTimPenggerak->alamat,
                'pendidikan' => $anggotaTimPenggerak->pendidikan,
                'pekerjaan' => $anggotaTimPenggerak->pekerjaan,
                'keterangan' => $anggotaTimPenggerak->keterangan,
            ],
        ]);
    }

    public function update(UpdateAnggotaTimPenggerakRequest $request, int $id): RedirectResponse
    {
        $anggotaTimPenggerak = $this->getScopedAnggotaTimPenggerakUseCase->execute($id, 'desa');
        $this->authorize('update', $anggotaTimPenggerak);
        $this->updateAnggotaTimPenggerakAction->execute($anggotaTimPenggerak, $request->validated());

        return redirect()->route('desa.anggota-tim-penggerak.index')->with('success', 'Data anggota tim penggerak berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $anggotaTimPenggerak = $this->getScopedAnggotaTimPenggerakUseCase->execute($id, 'desa');
        $this->authorize('delete', $anggotaTimPenggerak);
        $this->anggotaTimPenggerakRepository->delete($anggotaTimPenggerak);

        return redirect()->route('desa.anggota-tim-penggerak.index')->with('success', 'Data anggota tim penggerak berhasil dihapus');
    }
}


