<?php

namespace App\Domains\Wilayah\BukuKeuangan\Repositories;

use App\Domains\Wilayah\BukuKeuangan\DTOs\BukuKeuanganData;
use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuKeuanganRepository implements BukuKeuanganRepositoryInterface
{
    public function store(BukuKeuanganData $data): BukuKeuangan
    {
        return BukuKeuangan::create([
            'transaction_date' => $data->transaction_date,
            'source' => $data->source,
            'description' => $data->description,
            'reference_number' => $data->reference_number,
            'entry_type' => $data->entry_type,
            'amount' => $data->amount,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return BukuKeuangan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection
    {
        return BukuKeuangan::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('transaction_date')
            ->latest('id')
            ->get();
    }

    public function find(int $id): BukuKeuangan
    {
        return BukuKeuangan::findOrFail($id);
    }

    public function update(BukuKeuangan $bukuKeuangan, BukuKeuanganData $data): BukuKeuangan
    {
        $bukuKeuangan->update([
            'transaction_date' => $data->transaction_date,
            'source' => $data->source,
            'description' => $data->description,
            'reference_number' => $data->reference_number,
            'entry_type' => $data->entry_type,
            'amount' => $data->amount,
        ]);

        return $bukuKeuangan;
    }

    public function delete(BukuKeuangan $bukuKeuangan): void
    {
        $bukuKeuangan->delete();
    }
}




