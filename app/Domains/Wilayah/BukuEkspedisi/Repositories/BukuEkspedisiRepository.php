<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Repositories;

use App\Domains\Wilayah\BukuEkspedisi\DTOs\BukuEkspedisiData;
use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuEkspedisiRepository implements BukuEkspedisiRepositoryInterface
{
    public function store(BukuEkspedisiData $data): BukuEkspedisi
    {
        return BukuEkspedisi::create([
            'title' => $data->title,
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
        return BukuEkspedisi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection
    {
        return BukuEkspedisi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest()
            ->get();
    }

    public function find(int $id): BukuEkspedisi
    {
        return BukuEkspedisi::findOrFail($id);
    }

    public function update(BukuEkspedisi $bukuEkspedisi, BukuEkspedisiData $data): BukuEkspedisi
    {
        $bukuEkspedisi->update([
            'title' => $data->title,
            'file_path' => $data->file_path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'extension' => $data->extension,
            'size_bytes' => $data->size_bytes,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $bukuEkspedisi;
    }

    public function delete(BukuEkspedisi $bukuEkspedisi): void
    {
        $bukuEkspedisi->delete();
    }
}
