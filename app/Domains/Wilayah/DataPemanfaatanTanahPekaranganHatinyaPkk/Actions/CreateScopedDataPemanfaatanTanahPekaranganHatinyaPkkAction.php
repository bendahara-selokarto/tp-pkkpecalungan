<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Actions;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\DTOs\DataPemanfaatanTanahPekaranganHatinyaPkkData;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Repositories\DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface;
use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Services\DataPemanfaatanTanahPekaranganHatinyaPkkScopeService;

class CreateScopedDataPemanfaatanTanahPekaranganHatinyaPkkAction
{
    public function __construct(
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkRepositoryInterface $dataPemanfaatanTanahPekaranganHatinyaPkkRepository,
        private readonly DataPemanfaatanTanahPekaranganHatinyaPkkScopeService $dataPemanfaatanTanahPekaranganHatinyaPkkScopeService
    ) {
    }

    public function execute(array $payload, string $level): DataPemanfaatanTanahPekaranganHatinyaPkk
    {
        $data = DataPemanfaatanTanahPekaranganHatinyaPkkData::fromArray([
            'kategori_pemanfaatan_lahan' => $payload['kategori_pemanfaatan_lahan'],
            'komoditi' => $payload['komoditi'],
            'jumlah_komoditi' => $payload['jumlah_komoditi'],
            'level' => $level,
            'area_id' => $this->dataPemanfaatanTanahPekaranganHatinyaPkkScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->dataPemanfaatanTanahPekaranganHatinyaPkkRepository->store($data);
    }
}



