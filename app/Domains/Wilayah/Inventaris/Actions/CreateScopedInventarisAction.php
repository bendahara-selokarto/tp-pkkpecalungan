<?php

namespace App\Domains\Wilayah\Inventaris\Actions;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepositoryInterface;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;

class CreateScopedInventarisAction
{
    public function __construct(
        private readonly InventarisRepositoryInterface $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function execute(array $payload, string $level): Inventaris
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
            'level' => $level,
            'area_id' => $this->inventarisScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->inventarisRepository->store($data);
    }
}
