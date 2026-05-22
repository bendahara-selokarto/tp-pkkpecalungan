<?php

namespace App\Domains\Wilayah\BukuAgendaSk\Repositories;

use App\Domains\Wilayah\BukuAgendaSk\DTOs\BukuAgendaSkData;
use App\Domains\Wilayah\BukuAgendaSk\Models\BukuAgendaSk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BukuAgendaSkRepository implements BukuAgendaSkRepositoryInterface
{
    public function store(BukuAgendaSkData $data): BukuAgendaSk
    {
        return BukuAgendaSk::create([
            'nomor_sk' => $data->nomor_sk,
            'tanggal_sk' => $data->tanggal_sk,
            'kepada' => $data->kepada,
            'perihal' => $data->perihal,
            'tembusan' => $data->tembusan,
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
        return BukuAgendaSk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('tanggal_sk')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, ?int $creatorIdFilter = null): Collection
    {
        return BukuAgendaSk::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('tanggal_sk')
            ->latest('id')
            ->get();
    }

    public function find(int $id): BukuAgendaSk
    {
        return BukuAgendaSk::findOrFail($id);
    }

    public function update(BukuAgendaSk $bukuAgendaSk, BukuAgendaSkData $data): BukuAgendaSk
    {
        $bukuAgendaSk->update([
            'nomor_sk' => $data->nomor_sk,
            'tanggal_sk' => $data->tanggal_sk,
            'kepada' => $data->kepada,
            'perihal' => $data->perihal,
            'tembusan' => $data->tembusan,
            'file_path' => $data->file_path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'extension' => $data->extension,
            'size_bytes' => $data->size_bytes,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $bukuAgendaSk;
    }

    public function delete(BukuAgendaSk $bukuAgendaSk): void
    {
        $bukuAgendaSk->delete();
    }
}
