<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Controllers;

use App\Domains\Wilayah\DataPelatihanKader\Actions\CreateScopedDataPelatihanKaderAction;
use App\Domains\Wilayah\DataPelatihanKader\Actions\UpdateDataPelatihanKaderAction;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Requests\StoreDataPelatihanKaderRequest;
use App\Domains\Wilayah\DataPelatihanKader\Requests\UpdateDataPelatihanKaderRequest;
use App\Domains\Wilayah\DataPelatihanKader\UseCases\GetScopedDataPelatihanKaderUseCase;
use App\Domains\Wilayah\DataPelatihanKader\UseCases\ListScopedDataPelatihanKaderUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DesaDataPelatihanKaderController extends Controller
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly ListScopedDataPelatihanKaderUseCase $listScopedDataPelatihanKaderUseCase,
        private readonly GetScopedDataPelatihanKaderUseCase $getScopedDataPelatihanKaderUseCase,
        private readonly CreateScopedDataPelatihanKaderAction $createScopedDataPelatihanKaderAction,
        private readonly UpdateDataPelatihanKaderAction $updateDataPelatihanKaderAction
    ) {
        $this->middleware('scope.role:desa');
    }

    public function index(): Response
    {
        $this->authorize('viewAny', DataPelatihanKader::class);
        $items = $this->listScopedDataPelatihanKaderUseCase->execute(ScopeLevel::DESA->value);

        return Inertia::render('Desa/DataPelatihanKader/Index', [
            'dataPelatihanKaderItems' => $items->values()->map(fn (DataPelatihanKader $item) => [
                'id' => $item->id,
                'nomor_registrasi' => $item->nomor_registrasi,
                'nama_lengkap_kader' => $item->nama_lengkap_kader,
                'jabatan_fungsi' => $item->jabatan_fungsi,
                'judul_pelatihan' => $item->judul_pelatihan,
                'tahun_penyelenggaraan' => $item->tahun_penyelenggaraan,
                'status_sertifikat' => $item->status_sertifikat,
            ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', DataPelatihanKader::class);

        return Inertia::render('Desa/DataPelatihanKader/Create', [
            'statusSertifikatOptions' => DataPelatihanKader::statusSertifikatOptions(),
        ]);
    }

    public function store(StoreDataPelatihanKaderRequest $request): RedirectResponse
    {
        $this->authorize('create', DataPelatihanKader::class);
        $this->createScopedDataPelatihanKaderAction->execute($request->validated(), ScopeLevel::DESA->value);

        return redirect()->route('desa.data-pelatihan-kader.index')->with('success', 'Data Pelatihan Kader berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataPelatihanKader = $this->getScopedDataPelatihanKaderUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('view', $dataPelatihanKader);

        return Inertia::render('Desa/DataPelatihanKader/Show', [
            'dataPelatihanKader' => [
                'id' => $dataPelatihanKader->id,
                'nomor_registrasi' => $dataPelatihanKader->nomor_registrasi,
                'nama_lengkap_kader' => $dataPelatihanKader->nama_lengkap_kader,
                'tanggal_masuk_tp_pkk' => $dataPelatihanKader->tanggal_masuk_tp_pkk,
                'jabatan_fungsi' => $dataPelatihanKader->jabatan_fungsi,
                'nomor_urut_pelatihan' => $dataPelatihanKader->nomor_urut_pelatihan,
                'judul_pelatihan' => $dataPelatihanKader->judul_pelatihan,
                'jenis_kriteria_kaderisasi' => $dataPelatihanKader->jenis_kriteria_kaderisasi,
                'tahun_penyelenggaraan' => $dataPelatihanKader->tahun_penyelenggaraan,
                'institusi_penyelenggara' => $dataPelatihanKader->institusi_penyelenggara,
                'status_sertifikat' => $dataPelatihanKader->status_sertifikat,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataPelatihanKader = $this->getScopedDataPelatihanKaderUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataPelatihanKader);

        return Inertia::render('Desa/DataPelatihanKader/Edit', [
            'dataPelatihanKader' => [
                'id' => $dataPelatihanKader->id,
                'nomor_registrasi' => $dataPelatihanKader->nomor_registrasi,
                'nama_lengkap_kader' => $dataPelatihanKader->nama_lengkap_kader,
                'tanggal_masuk_tp_pkk' => $dataPelatihanKader->tanggal_masuk_tp_pkk,
                'jabatan_fungsi' => $dataPelatihanKader->jabatan_fungsi,
                'nomor_urut_pelatihan' => $dataPelatihanKader->nomor_urut_pelatihan,
                'judul_pelatihan' => $dataPelatihanKader->judul_pelatihan,
                'jenis_kriteria_kaderisasi' => $dataPelatihanKader->jenis_kriteria_kaderisasi,
                'tahun_penyelenggaraan' => $dataPelatihanKader->tahun_penyelenggaraan,
                'institusi_penyelenggara' => $dataPelatihanKader->institusi_penyelenggara,
                'status_sertifikat' => $dataPelatihanKader->status_sertifikat,
            ],
            'statusSertifikatOptions' => DataPelatihanKader::statusSertifikatOptions(),
        ]);
    }

    public function update(UpdateDataPelatihanKaderRequest $request, int $id): RedirectResponse
    {
        $dataPelatihanKader = $this->getScopedDataPelatihanKaderUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('update', $dataPelatihanKader);
        $this->updateDataPelatihanKaderAction->execute($dataPelatihanKader, $request->validated());

        return redirect()->route('desa.data-pelatihan-kader.index')->with('success', 'Data Pelatihan Kader berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataPelatihanKader = $this->getScopedDataPelatihanKaderUseCase->execute($id, ScopeLevel::DESA->value);
        $this->authorize('delete', $dataPelatihanKader);
        $this->dataPelatihanKaderRepository->delete($dataPelatihanKader);

        return redirect()->route('desa.data-pelatihan-kader.index')->with('success', 'Data Pelatihan Kader berhasil dihapus');
    }
}
