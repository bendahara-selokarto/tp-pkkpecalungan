<?php

namespace App\Domains\Wilayah\Kliping\Repositories;

use App\Domains\Wilayah\Kliping\DTOs\KlipingData;
use App\Domains\Wilayah\Kliping\Models\Kliping;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KlipingRepository implements KlipingRepositoryInterface
{
    public function store(KlipingData $data): Kliping
    {
        return Kliping::create([
            'date' => $data->date,
            'description' => $data->description,
            'file_path' => $data->file_path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'extension' => $data->extension,
            'size_bytes' => $data->size_bytes,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return Kliping::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection
    {
        return Kliping::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('date')
            ->latest('id')
            ->get();
    }

    public function find(int $id): Kliping
    {
        return Kliping::findOrFail($id);
    }

    public function update(Kliping $kliping, KlipingData $data): Kliping
    {
        $kliping->update([
            'date' => $data->date,
            'description' => $data->description,
            'file_path' => $data->file_path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'extension' => $data->extension,
            'size_bytes' => $data->size_bytes,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $kliping;
    }

    public function delete(Kliping $kliping): void
    {
        $kliping->delete();
    }
}
