<?php

namespace App\Domains\Wilayah\DataKeluarga\Actions;

use App\Domains\Wilayah\DataKeluarga\DTOs\DataKeluargaData;
use App\Domains\Wilayah\DataKeluarga\Models\DataKeluarga;
use App\Domains\Wilayah\DataKeluarga\Repositories\DataKeluargaRepositoryInterface;

class UpdateDataKeluargaAction
{
    public function __construct(
        private readonly DataKeluargaRepositoryInterface $dataKeluargaRepository
    ) {
    }

    public function execute(DataKeluarga $dataKeluarga, array $payload): DataKeluarga
    {
        $data = DataKeluargaData::fromArray([
            'kategori_keluarga' => $payload['kategori_keluarga'],
            'jumlah_keluarga' => $payload['jumlah_keluarga'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $dataKeluarga->level,
            'area_id' => $dataKeluarga->area_id,
            'created_by' => $dataKeluarga->created_by,
        ]);

        return $this->dataKeluargaRepository->update($dataKeluarga, $data);
    }
}

