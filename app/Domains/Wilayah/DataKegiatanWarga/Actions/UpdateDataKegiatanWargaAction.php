<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Actions;

use App\Domains\Wilayah\DataKegiatanWarga\DTOs\DataKegiatanWargaData;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\DataKegiatanWarga\Repositories\DataKegiatanWargaRepositoryInterface;

class UpdateDataKegiatanWargaAction
{
    public function __construct(
        private readonly DataKegiatanWargaRepositoryInterface $dataKegiatanWargaRepository
    ) {
    }

    public function execute(DataKegiatanWarga $dataKegiatanWarga, array $payload): DataKegiatanWarga
    {
        $data = DataKegiatanWargaData::fromArray([
            'kegiatan' => $payload['kegiatan'],
            'aktivitas' => $payload['aktivitas'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $dataKegiatanWarga->level,
            'area_id' => $dataKegiatanWarga->area_id,
            'created_by' => $dataKegiatanWarga->created_by,
        ]);

        return $this->dataKegiatanWargaRepository->update($dataKegiatanWarga, $data);
    }
}
