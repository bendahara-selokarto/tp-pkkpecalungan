<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Actions;

use App\Domains\Wilayah\DataPelatihanKader\DTOs\DataPelatihanKaderData;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;

class UpdateDataPelatihanKaderAction
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository
    ) {
    }

    public function execute(DataPelatihanKader $dataPelatihanKader, array $payload): DataPelatihanKader
    {
        $data = DataPelatihanKaderData::fromArray([
            'nomor_registrasi' => $payload['nomor_registrasi'],
            'nama_lengkap_kader' => $payload['nama_lengkap_kader'],
            'tanggal_masuk_tp_pkk' => $payload['tanggal_masuk_tp_pkk'],
            'jabatan_fungsi' => $payload['jabatan_fungsi'],
            'nomor_urut_pelatihan' => $payload['nomor_urut_pelatihan'],
            'judul_pelatihan' => $payload['judul_pelatihan'],
            'jenis_kriteria_kaderisasi' => $payload['jenis_kriteria_kaderisasi'],
            'tahun_penyelenggaraan' => $payload['tahun_penyelenggaraan'],
            'institusi_penyelenggara' => $payload['institusi_penyelenggara'],
            'status_sertifikat' => $payload['status_sertifikat'],
            'level' => $dataPelatihanKader->level,
            'area_id' => $dataPelatihanKader->area_id,
            'created_by' => $dataPelatihanKader->created_by,
        ]);

        return $this->dataPelatihanKaderRepository->update($dataPelatihanKader, $data);
    }
}
