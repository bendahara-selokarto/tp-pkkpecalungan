<?php

namespace App\Domains\Wilayah\AnggotaPokja\Controllers;

use App\Domains\Wilayah\AnggotaPokja\Actions\CreateScopedAnggotaPokjaAction;
use App\Domains\Wilayah\AnggotaPokja\Actions\UpdateAnggotaPokjaAction;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Requests\ListAnggotaPokjaRequest;
use App\Domains\Wilayah\AnggotaPokja\Requests\StoreAnggotaPokjaRequest;
use App\Domains\Wilayah\AnggotaPokja\Requests\UpdateAnggotaPokjaRequest;
use App\Domains\Wilayah\AnggotaPokja\UseCases\GetScopedAnggotaPokjaUseCase;
use App\Domains\Wilayah\AnggotaPokja\UseCases\ListScopedAnggotaPokjaUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaAnggotaPokjaController extends Controller
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly ListScopedAnggotaPokjaUseCase $listScopedAnggotaPokjaUseCase,
        private readonly GetScopedAnggotaPokjaUseCase $getScopedAnggotaPokjaUseCase,
        private readonly CreateScopedAnggotaPokjaAction $createScopedAnggotaPokjaAction,
        private readonly UpdateAnggotaPokjaAction $updateAnggotaPokjaAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(ListAnggotaPokjaRequest $request): Response
    {
        $this->authorize('viewAny', AnggotaPokja::class);
        $anggotaPokjas = $this->listScopedAnggotaPokjaUseCase
            ->execute('desa', $request->perPage())
            ->through(fn (AnggotaPokja $item) => [
                'id' => $item->id,
                'nama' => $item->nama,
                'jabatan' => $item->jabatan,
                'pokja' => $item->pokja,
                'jenis_kelamin' => $item->jenis_kelamin,
                'umur' => $item->umur,
            ]);

        return Inertia::render('Desa/AnggotaPokja/Index', [
            'anggotaPokjas' => $anggotaPokjas,
            'pagination' => [
                'perPageOptions' => [10, 25, 50],
            ],
            'filters' => [
                'per_page' => $request->perPage(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', AnggotaPokja::class);

        return Inertia::render('Desa/AnggotaPokja/Create');
    }

    public function store(StoreAnggotaPokjaRequest $request): RedirectResponse
    {
        $this->authorize('create', AnggotaPokja::class);
        $this->createScopedAnggotaPokjaAction->execute($request->validated(), 'desa');

        return redirect()->route('desa.anggota-pokja.index')->with('success', 'Data anggota pokja berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $anggotaPokja = $this->getScopedAnggotaPokjaUseCase->execute($id, 'desa');
        $this->authorize('view', $anggotaPokja);

        return Inertia::render('Desa/AnggotaPokja/Show', [
            'anggotaPokja' => [
                'id' => $anggotaPokja->id,
                'nama' => $anggotaPokja->nama,
                'jabatan' => $anggotaPokja->jabatan,
                'jenis_kelamin' => $anggotaPokja->jenis_kelamin,
                'tempat_lahir' => $anggotaPokja->tempat_lahir,
                'tanggal_lahir' => optional($anggotaPokja->tanggal_lahir)->format('Y-m-d'),
                'umur' => $anggotaPokja->umur,
                'status_perkawinan' => $anggotaPokja->status_perkawinan,
                'alamat' => $anggotaPokja->alamat,
                'pendidikan' => $anggotaPokja->pendidikan,
                'pekerjaan' => $anggotaPokja->pekerjaan,
                'keterangan' => $anggotaPokja->keterangan,
                'pokja' => $anggotaPokja->pokja,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $anggotaPokja = $this->getScopedAnggotaPokjaUseCase->execute($id, 'desa');
        $this->authorize('update', $anggotaPokja);

        return Inertia::render('Desa/AnggotaPokja/Edit', [
            'anggotaPokja' => [
                'id' => $anggotaPokja->id,
                'nama' => $anggotaPokja->nama,
                'jabatan' => $anggotaPokja->jabatan,
                'jenis_kelamin' => $anggotaPokja->jenis_kelamin,
                'tempat_lahir' => $anggotaPokja->tempat_lahir,
                'tanggal_lahir' => optional($anggotaPokja->tanggal_lahir)->format('Y-m-d'),
                'status_perkawinan' => $anggotaPokja->status_perkawinan,
                'alamat' => $anggotaPokja->alamat,
                'pendidikan' => $anggotaPokja->pendidikan,
                'pekerjaan' => $anggotaPokja->pekerjaan,
                'keterangan' => $anggotaPokja->keterangan,
                'pokja' => $anggotaPokja->pokja,
            ],
        ]);
    }

    public function update(UpdateAnggotaPokjaRequest $request, int $id): RedirectResponse
    {
        $anggotaPokja = $this->getScopedAnggotaPokjaUseCase->execute($id, 'desa');
        $this->authorize('update', $anggotaPokja);
        $this->updateAnggotaPokjaAction->execute($anggotaPokja, $request->validated());

        return redirect()->route('desa.anggota-pokja.index')->with('success', 'Data anggota pokja berhasil diupdate');
    }

    public function destroy(int $id): RedirectResponse
    {
        $anggotaPokja = $this->getScopedAnggotaPokjaUseCase->execute($id, 'desa');
        $this->authorize('delete', $anggotaPokja);
        $this->anggotaPokjaRepository->delete($anggotaPokja);

        return redirect()->route('desa.anggota-pokja.index')->with('success', 'Data anggota pokja berhasil dihapus');
    }
}
