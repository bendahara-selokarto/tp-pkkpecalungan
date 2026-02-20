<?php

namespace App\Domains\Wilayah\PrestasiLomba\Actions;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use App\Domains\Wilayah\PrestasiLomba\Repositories\PrestasiLombaRepositoryInterface;

class UpdatePrestasiLombaAction
{
    public function __construct(
        private readonly PrestasiLombaRepositoryInterface $prestasiLombaRepository
    ) {
    }

    public function execute(PrestasiLomba $prestasiLomba, array $payload): PrestasiLomba
    {
        $data = PrestasiLombaData::fromArray([
            'tahun' => $payload['tahun'],
            'jenis_lomba' => $payload['jenis_lomba'],
            'lokasi' => $payload['lokasi'],
            'prestasi_kecamatan' => $payload['prestasi_kecamatan'],
            'prestasi_kabupaten' => $payload['prestasi_kabupaten'],
            'prestasi_provinsi' => $payload['prestasi_provinsi'],
            'prestasi_nasional' => $payload['prestasi_nasional'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $prestasiLomba->level,
            'area_id' => $prestasiLomba->area_id,
            'created_by' => $prestasiLomba->created_by,
        ]);

        return $this->prestasiLombaRepository->update($prestasiLomba, $data);
    }
}
