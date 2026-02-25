<?php

namespace App\Domains\Wilayah\Paar\Repositories;

use App\Domains\Wilayah\Paar\DTOs\PaarData;
use App\Domains\Wilayah\Paar\Models\Paar;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PaarRepository implements PaarRepositoryInterface
{
    public function store(PaarData $data): Paar
    {
        $existing = Paar::query()
            ->where('level', $data->level)
            ->where('area_id', $data->area_id)
            ->where('indikator', $data->indikator)
            ->first();

        if ($existing instanceof Paar) {
            $existing->update([
                'jumlah' => $data->jumlah,
                'keterangan' => $data->keterangan,
            ]);

            return $existing;
        }

        return Paar::create([
            'indikator' => $data->indikator,
            'jumlah' => $data->jumlah,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return $this->scopedQuery($level, $areaId)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return $this->scopedQuery($level, $areaId)->get();
    }

    public function find(int $id): Paar
    {
        return Paar::findOrFail($id);
    }

    public function update(Paar $paar, PaarData $data): Paar
    {
        $paar->update([
            'jumlah' => $data->jumlah,
            'keterangan' => $data->keterangan,
        ]);

        return $paar;
    }

    public function delete(Paar $paar): void
    {
        $paar->delete();
    }

    private function scopedQuery(string $level, int $areaId): Builder
    {
        $orderByIndicator = implode(' ', array_map(
            static fn (string $indicator, int $index): string => sprintf("WHEN '%s' THEN %d", $indicator, $index),
            Paar::indicatorKeys(),
            array_keys(Paar::indicatorKeys())
        ));

        return Paar::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->orderByRaw("CASE indikator {$orderByIndicator} ELSE 999 END")
            ->orderBy('id');
    }
}
