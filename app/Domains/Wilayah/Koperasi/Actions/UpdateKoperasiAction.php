<?php

namespace App\Domains\Wilayah\Koperasi\Actions;

use App\Domains\Wilayah\Koperasi\DTOs\KoperasiData;
use App\Domains\Wilayah\Koperasi\Models\Koperasi;
use App\Domains\Wilayah\Koperasi\Repositories\KoperasiRepositoryInterface;

class UpdateKoperasiAction
{
    public function __construct(
        private readonly KoperasiRepositoryInterface $koperasiRepository
    ) {
    }

    public function execute(Koperasi $koperasi, array $payload): Koperasi
    {
        $data = KoperasiData::fromArray([
            'nama_koperasi' => $payload['nama_koperasi'],
            'jenis_usaha' => $payload['jenis_usaha'],
            'berbadan_hukum' => $payload['berbadan_hukum'],
            'belum_berbadan_hukum' => $payload['belum_berbadan_hukum'],
            'jumlah_anggota_l' => $payload['jumlah_anggota_l'],
            'jumlah_anggota_p' => $payload['jumlah_anggota_p'],
            'level' => $koperasi->level,
            'area_id' => $koperasi->area_id,
            'created_by' => $koperasi->created_by,
        ]);

        return $this->koperasiRepository->update($koperasi, $data);
    }
}


