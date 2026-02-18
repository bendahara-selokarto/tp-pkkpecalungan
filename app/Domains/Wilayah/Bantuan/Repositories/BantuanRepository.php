<?php

namespace App\Domains\Wilayah\Bantuan\Repositories;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use Illuminate\Support\Collection;

class BantuanRepository implements BantuanRepositoryInterface
{
    public function store(BantuanData $data): Bantuan
    {
        return Bantuan::create([
            'name' => $data->name,
            'category' => $data->category,
            'description' => $data->description,
            'source' => $data->source,
            'amount' => $data->amount,
            'received_date' => $data->received_date,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return Bantuan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('received_date')
            ->latest('id')
            ->get();
    }

    public function find(int $id): Bantuan
    {
        return Bantuan::findOrFail($id);
    }

    public function update(Bantuan $bantuan, BantuanData $data): Bantuan
    {
        $bantuan->update([
            'name' => $data->name,
            'category' => $data->category,
            'description' => $data->description,
            'source' => $data->source,
            'amount' => $data->amount,
            'received_date' => $data->received_date,
        ]);

        return $bantuan;
    }

    public function delete(Bantuan $bantuan): void
    {
        $bantuan->delete();
    }
}

