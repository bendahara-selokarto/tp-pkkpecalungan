<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\DTOs\DataPemanfaatanTanahPekaranganHatinyaPkkData;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;

class UpdateDataPemanfaatanTanahPekaranganHatinyaPkkAction
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository
    ) {
    }

    public function execute(DataPemanfaatanTanahPekaranganHatinyaPkk $dataPemanfaatanTanahPekaranganHatinyaPkk, array $payload): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        $data = DataPemanfaatanTanahPekaranganHatinyaPkkData::fromArray([
            'kategori_pemanfaatan_lahan' => $payload['kategori_pemanfaatan_lahan'],
            'komoditi' => $payload['komoditi'],
            'jumlah_komoditi' => $payload['jumlah_komoditi'],
            'level' => $dataPemanfaatanTanahPekaranganHatinyaPkk->level,
            'area_id' => $dataPemanfaatanTanahPekaranganHatinyaPkk->area_id,
            'created_by' => $dataPemanfaatanTanahPekaranganHatinyaPkk->created_by,
        ]);

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->update($dataPemanfaatanTanahPekaranganHatinyaPkk, $data);
    }
}



