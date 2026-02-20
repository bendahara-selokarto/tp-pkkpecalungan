<?php

namespace App\Domains\Wilayah\DataPelatihanKader\Actions;

use App\Domains\Wilayah\DataPelatihanKader\DTOs\DataPelatihanKaderData;
use App\Domains\Wilayah\DataPelatihanKader\Models\DataPelatihanKader;
use App\Domains\Wilayah\DataPelatihanKader\Repositories\DataPelatihanKaderRepositoryInterface;
use App\Domains\Wilayah\DataPelatihanKader\Services\DataPelatihanKaderScopeService;

class CreateScopedDataPelatihanKaderAction
{
    public function __construct(
        private readonly DataPelatihanKaderRepositoryInterface $dataPelatihanKaderRepository,
        private readonly DataPelatihanKaderScopeService $dataPelatihanKaderScopeService
    ) {
    }

    public function execute(array $payload, string $level): DataPelatihanKader
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
            'level' => $level,
            'area_id' => $this->dataPelatihanKaderScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->dataPelatihanKaderRepository->store($data);
    }
}
