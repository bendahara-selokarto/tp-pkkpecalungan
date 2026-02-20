<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Actions;

use App\Domains\Wilayah\DataIndustriRumahTangga\DTOs\DataIndustriRumahTanggaData;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use App\Domains\Wilayah\DataIndustriRumahTangga\Repositories\DataIndustriRumahTanggaRepositoryInterface;
use App\Domains\Wilayah\DataIndustriRumahTangga\Services\DataIndustriRumahTanggaScopeService;

class CreateScopedDataIndustriRumahTanggaAction
{
    public function __construct(
        private readonly DataIndustriRumahTanggaRepositoryInterface $dataIndustriRumahTanggaRepository,
        private readonly DataIndustriRumahTanggaScopeService $dataIndustriRumahTanggaScopeService
    ) {
    }

    public function execute(array $payload, string $level): DataIndustriRumahTangga
    {
        $data = DataIndustriRumahTanggaData::fromArray([
            'kategori_jenis_industri' => $payload['kategori_jenis_industri'],
            'komoditi' => $payload['komoditi'],
            'jumlah_komoditi' => $payload['jumlah_komoditi'],
            'level' => $level,
            'area_id' => $this->dataIndustriRumahTanggaScopeService->requireUserAreaId(),
            'created_by' => (int) auth()->id(),
        ]);

        return $this->dataIndustriRumahTanggaRepository->store($data);
    }
}




