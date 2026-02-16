<?php

namespace App\Domains\Wilayah\Inventaris\Actions;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use App\Domains\Wilayah\Inventaris\Repositories\InventarisRepository;
use App\Domains\Wilayah\Inventaris\Services\InventarisScopeService;

class CreateScopedInventarisAction
{
    public function __construct(
        private readonly InventarisRepository $inventarisRepository,
        private readonly InventarisScopeService $inventarisScopeService
    ) {
    }

    public function execute(array $payload, string $level): Inventaris
    {
        $data = InventarisData::fromArray([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'quantity' => $payload['quantity'],
            'unit' => $payload['unit'],
            'condition' => $payload['condition'],
            'level' => $level,
            'area_id' => $this->inventarisScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->inventarisRepository->store($data);
    }
}
