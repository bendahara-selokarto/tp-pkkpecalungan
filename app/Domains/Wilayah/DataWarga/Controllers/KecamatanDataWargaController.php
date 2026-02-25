<?php

namespace App\Domains\Wilayah\DataWarga\Controllers;

use App\Domains\Wilayah\DataWarga\Actions\CreateScopedDataWargaAction;
use App\Domains\Wilayah\DataWarga\Actions\UpdateDataWargaAction;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;
use App\Domains\Wilayah\DataWarga\Requests\ListDataWargaRequest;
use App\Domains\Wilayah\DataWarga\Requests\StoreDataWargaRequest;
use App\Domains\Wilayah\DataWarga\Requests\UpdateDataWargaRequest;
use App\Domains\Wilayah\DataWarga\UseCases\GetScopedDataWargaUseCase;
use App\Domains\Wilayah\DataWarga\UseCases\ListScopedDataWargaUseCase;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KecamatanDataWargaController extends Controller
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository,
        private readonly ListScopedDataWargaUseCase $listScopedDataWargaUseCase,
        private readonly GetScopedDataWargaUseCase $getScopedDataWargaUseCase,
        private readonly CreateScopedDataWargaAction $createScopedDataWargaAction,
        private readonly UpdateDataWargaAction $updateDataWargaAction
    ) {
        $this->middleware('scope.role:kecamatan');
    }

    public function index(ListDataWargaRequest $request): Response
    {
        $this->authorize('viewAny', DataWarga::class);
        $items = $this->listScopedDataWargaUseCase
            ->execute(ScopeLevel::KECAMATAN->value, $request->perPage())
            ->through(fn (DataWarga $item) => [
                'id' => $item->id,
                'dasawisma' => $item->dasawisma,
                'nama_kepala_keluarga' => $item->nama_kepala_keluarga,
                'alamat' => $item->alamat,
                'jumlah_warga_laki_laki' => $item->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $item->jumlah_warga_perempuan,
                'total_warga' => $item->total_warga,
                'keterangan' => $item->keterangan,
            ]);

        return Inertia::render('Kecamatan/DataWarga/Index', [
            'dataWargaItems' => $items,
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
        $this->authorize('create', DataWarga::class);

        return Inertia::render('Kecamatan/DataWarga/Create');
    }

    public function store(StoreDataWargaRequest $request): RedirectResponse
    {
        $this->authorize('create', DataWarga::class);
        $this->createScopedDataWargaAction->execute($request->validated(), ScopeLevel::KECAMATAN->value);

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil dibuat');
    }

    public function show(int $id): Response
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('view', $dataWarga);

        return Inertia::render('Kecamatan/DataWarga/Show', [
            'dataWarga' => [
                'id' => $dataWarga->id,
                'dasawisma' => $dataWarga->dasawisma,
                'nama_kepala_keluarga' => $dataWarga->nama_kepala_keluarga,
                'alamat' => $dataWarga->alamat,
                'jumlah_warga_laki_laki' => $dataWarga->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $dataWarga->jumlah_warga_perempuan,
                'total_warga' => $dataWarga->total_warga,
                'keterangan' => $dataWarga->keterangan,
            ],
        ]);
    }

    public function edit(int $id): Response
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataWarga);

        return Inertia::render('Kecamatan/DataWarga/Edit', [
            'dataWarga' => [
                'id' => $dataWarga->id,
                'dasawisma' => $dataWarga->dasawisma,
                'nama_kepala_keluarga' => $dataWarga->nama_kepala_keluarga,
                'alamat' => $dataWarga->alamat,
                'jumlah_warga_laki_laki' => $dataWarga->jumlah_warga_laki_laki,
                'jumlah_warga_perempuan' => $dataWarga->jumlah_warga_perempuan,
                'keterangan' => $dataWarga->keterangan,
                'anggota' => $dataWarga->anggota->values()->map(fn ($anggota) => [
                    'nomor_urut' => $anggota->nomor_urut,
                    'nomor_registrasi' => $anggota->nomor_registrasi,
                    'nomor_ktp_kk' => $anggota->nomor_ktp_kk,
                    'nama' => $anggota->nama,
                    'jabatan' => $anggota->jabatan,
                    'jenis_kelamin' => $anggota->jenis_kelamin,
                    'tempat_lahir' => $anggota->tempat_lahir,
                    'tanggal_lahir' => optional($anggota->tanggal_lahir)->format('Y-m-d'),
                    'umur_tahun' => $anggota->umur_tahun,
                    'status_perkawinan' => $anggota->status_perkawinan,
                    'status_dalam_keluarga' => $anggota->status_dalam_keluarga,
                    'agama' => $anggota->agama,
                    'alamat' => $anggota->alamat,
                    'desa_kel_sejenis' => $anggota->desa_kel_sejenis,
                    'pendidikan' => $anggota->pendidikan,
                    'pekerjaan' => $anggota->pekerjaan,
                    'akseptor_kb' => (bool) $anggota->akseptor_kb,
                    'aktif_posyandu' => (bool) $anggota->aktif_posyandu,
                    'ikut_bkb' => (bool) $anggota->ikut_bkb,
                    'memiliki_tabungan' => (bool) $anggota->memiliki_tabungan,
                    'ikut_kelompok_belajar' => (bool) $anggota->ikut_kelompok_belajar,
                    'jenis_kelompok_belajar' => $anggota->jenis_kelompok_belajar,
                    'ikut_paud' => (bool) $anggota->ikut_paud,
                    'ikut_koperasi' => (bool) $anggota->ikut_koperasi,
                    'keterangan' => $anggota->keterangan,
                ]),
            ],
        ]);
    }

    public function update(UpdateDataWargaRequest $request, int $id): RedirectResponse
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('update', $dataWarga);
        $this->updateDataWargaAction->execute($dataWarga, $request->validated());

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil diperbarui');
    }

    public function destroy(int $id): RedirectResponse
    {
        $dataWarga = $this->getScopedDataWargaUseCase->execute($id, ScopeLevel::KECAMATAN->value);
        $this->authorize('delete', $dataWarga);
        $this->dataWargaRepository->delete($dataWarga);

        return redirect()->route('kecamatan.data-warga.index')->with('success', 'Data warga berhasil dihapus');
    }
}
