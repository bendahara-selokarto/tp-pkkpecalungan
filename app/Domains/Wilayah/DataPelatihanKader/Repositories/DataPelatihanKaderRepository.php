<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Repositories;

use App\Domains\Wilayah\DataPelatihanKader\DTOs\DataPelatihanKaderData;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataPelatihanKaderRepository implements DataPelatihanKaderRepositoryInterface
{
    public function store(DataPelatihanKaderData $data): DataPelatihanKader
    {
        return DataPelatihanKader::create([
            'nomor_registrasi' => $data->nomor_registrasi,
            'nama_lengkap_kader' => $data->nama_lengkap_kader,
            'tanggal_masuk_tp_pkk' => $data->tanggal_masuk_tp_pkk,
            'jabatan_fungsi' => $data->jabatan_fungsi,
            'nomor_urut_pelatihan' => $data->nomor_urut_pelatihan,
            'judul_pelatihan' => $data->judul_pelatihan,
            'jenis_kriteria_kaderisasi' => $data->jenis_kriteria_kaderisasi,
            'tahun_penyelenggaraan' => $data->tahun_penyelenggaraan,
            'institusi_penyelenggara' => $data->institusi_penyelenggara,
            'status_sertifikat' => $data->status_sertifikat,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return DataPelatihanKader::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return DataPelatihanKader::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataPelatihanKader
    {
        return DataPelatihanKader::findOrFail($id);
    }

    public function update(DataPelatihanKader $dataPelatihanKader, DataPelatihanKaderData $data): DataPelatihanKader
    {
        $dataPelatihanKader->update([
            'nomor_registrasi' => $data->nomor_registrasi,
            'nama_lengkap_kader' => $data->nama_lengkap_kader,
            'tanggal_masuk_tp_pkk' => $data->tanggal_masuk_tp_pkk,
            'jabatan_fungsi' => $data->jabatan_fungsi,
            'nomor_urut_pelatihan' => $data->nomor_urut_pelatihan,
            'judul_pelatihan' => $data->judul_pelatihan,
            'jenis_kriteria_kaderisasi' => $data->jenis_kriteria_kaderisasi,
            'tahun_penyelenggaraan' => $data->tahun_penyelenggaraan,
            'institusi_penyelenggara' => $data->institusi_penyelenggara,
            'status_sertifikat' => $data->status_sertifikat,
        ]);

        return $dataPelatihanKader;
    }

    public function delete(DataPelatihanKader $dataPelatihanKader): void
    {
        $dataPelatihanKader->delete();
    }
}
