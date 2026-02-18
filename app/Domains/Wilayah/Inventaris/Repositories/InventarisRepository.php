<?php

namespace App\Domains\Wilayah\Inventaris\Repositories;

use App\Domains\Wilayah\Inventaris\DTOs\InventarisData;
use App\Domains\Wilayah\Inventaris\Models\Inventaris;
use Illuminate\Support\Collection;

class InventarisRepository implements InventarisRepositoryInterface
{
    public function store(InventarisData $data): Inventaris
    {
        return Inventaris::create([
            'name' => $data->name,
            'description' => $data->description,
            'quantity' => $data->quantity,
            'unit' => $data->unit,
            'condition' => $data->condition,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return Inventaris::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): Inventaris
    {
        return Inventaris::findOrFail($id);
    }

    public function update(Inventaris $inventaris, InventarisData $data): Inventaris
    {
        $inventaris->update([
            'name' => $data->name,
            'description' => $data->description,
            'quantity' => $data->quantity,
            'unit' => $data->unit,
            'condition' => $data->condition,
        ]);

        return $inventaris;
    }

    public function delete(Inventaris $inventaris): void
    {
        $inventaris->delete();
    }
}

