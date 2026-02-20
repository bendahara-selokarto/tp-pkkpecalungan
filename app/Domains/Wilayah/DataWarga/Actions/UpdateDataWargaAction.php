<?php

namespace App\Domains\Wilayah\DataWarga\Actions;

use App\Domains\Wilayah\DataWarga\DTOs\DataWargaData;
use App\Domains\Wilayah\DataWarga\Models\DataWarga;
use App\Domains\Wilayah\DataWarga\Repositories\DataWargaRepositoryInterface;

class UpdateDataWargaAction
{
    public function __construct(
        private readonly DataWargaRepositoryInterface $dataWargaRepository
    ) {
    }

    public function execute(DataWarga $dataWarga, array $payload): DataWarga
    {
        $data = DataWargaData::fromArray([
            'dasawisma' => $payload['dasawisma'],
            'nama_kepala_keluarga' => $payload['nama_kepala_keluarga'],
            'alamat' => $payload['alamat'],
            'jumlah_warga_laki_laki' => $payload['jumlah_warga_laki_laki'],
            'jumlah_warga_perempuan' => $payload['jumlah_warga_perempuan'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $dataWarga->level,
            'area_id' => $dataWarga->area_id,
            'created_by' => $dataWarga->created_by,
        ]);

        return $this->dataWargaRepository->update($dataWarga, $data);
    }
}
