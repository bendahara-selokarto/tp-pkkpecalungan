<?php

namespace App\Domains\Wilayah\Inventaris\Actions;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepository;

class UpdateInventarisAction
{
    public function __construct(
        private readonly InventarisRepository $inventarisRepository
    ) {
    }

    public function execute(Inventaris $inventaris, array $payload): Inventaris
    {
        $data = InventarisData::fromArray([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'quantity' => $payload['quantity'],
            'unit' => $payload['unit'],
            'condition' => $payload['condition'],
            'level' => $inventaris->level,
            'area_id' => $inventaris->area_id,
            'created_by' => $inventaris->created_by,
        ]);

        return $this->inventarisRepository->update($inventaris, $data);
    }
}
