<?php

namespace App\Domains\Wilayah\Inventaris\Actions;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;

class UpdateInventarisAction
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository
    ) {
    }

    public function execute(Inventaris $inventaris, array $payload): Inventaris
    {
        $data = InventarisData::fromArray([
            'name' => $payload['name'],
            'asal_barang' => $payload['asal_barang'] ?? null,
            'description' => $payload['description'] ?? $payload['keterangan'] ?? null,
            'keterangan' => $payload['keterangan'] ?? $payload['description'] ?? null,
            'quantity' => $payload['quantity'],
            'unit' => $payload['unit'],
            'tanggal_penerimaan' => $payload['tanggal_penerimaan'] ?? null,
            'tempat_penyimpanan' => $payload['tempat_penyimpanan'] ?? null,
            'condition' => $payload['condition'],
            'level' => $inventaris->level,
            'area_id' => $inventaris->area_id,
            'created_by' => $inventaris->created_by,
        ]);

        return $this->inventarisRepository->update($inventaris, $data);
    }
}
