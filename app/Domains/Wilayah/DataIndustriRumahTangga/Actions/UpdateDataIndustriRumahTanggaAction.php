<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Actions;

use App\Domains\Wilayah\DataIndustriRumahTangga\DTOs\DataIndustriRumahTanggaData;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;

class UpdateDataIndustriRumahTanggaAction
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository
    ) {
    }

    public function execute(DataIndustriRumahTangga $dataIndustriRumahTangga, array $payload): DataIndustriRumahTangga
    {
        $data = DataIndustriRumahTanggaData::fromArray([
            'kategori_jenis_industri' => $payload['kategori_jenis_industri'],
            'komoditi' => $payload['komoditi'],
            'jumlah_komoditi' => $payload['jumlah_komoditi'],
            'level' => $dataIndustriRumahTangga->level,
            'area_id' => $dataIndustriRumahTangga->area_id,
            'created_by' => $dataIndustriRumahTangga->created_by,
        ]);

        return $this->dataIndustriRumahTanggaRepository->update($dataIndustriRumahTangga, $data);
    }
}




