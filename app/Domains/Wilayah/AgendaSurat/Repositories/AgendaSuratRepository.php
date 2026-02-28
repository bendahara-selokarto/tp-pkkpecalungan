<?php

namespace App\Domains\Wilayah\AgendaSurat\Repositories;

use App\Domains\Wilayah\AgendaSurat\DTOs\AgendaSuratData;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AgendaSuratRepository implements AgendaSuratRepositoryInterface
{
    public function store(AgendaSuratData $data): AgendaSurat
    {
        return AgendaSurat::create([
            'jenis_surat' => $data->jenis_surat,
            'tanggal_terima' => $data->tanggal_terima,
            'tanggal_surat' => $data->tanggal_surat,
            'nomor_surat' => $data->nomor_surat,
            'asal_surat' => $data->asal_surat,
            'dari' => $data->dari,
            'kepada' => $data->kepada,
            'perihal' => $data->perihal,
            'lampiran' => $data->lampiran,
            'diteruskan_kepada' => $data->diteruskan_kepada,
            'tembusan' => $data->tembusan,
            'keterangan' => $data->keterangan,
            'data_dukung_path' => $data->data_dukung_path,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return AgendaSurat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('tanggal_surat')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection
    {
        return AgendaSurat::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('tanggal_surat')
            ->latest('id')
            ->get();
    }

    public function find(int $id): AgendaSurat
    {
        return AgendaSurat::findOrFail($id);
    }

    public function update(AgendaSurat $agendaSurat, AgendaSuratData $data): AgendaSurat
    {
        $agendaSurat->update([
            'jenis_surat' => $data->jenis_surat,
            'tanggal_terima' => $data->tanggal_terima,
            'tanggal_surat' => $data->tanggal_surat,
            'nomor_surat' => $data->nomor_surat,
            'asal_surat' => $data->asal_surat,
            'dari' => $data->dari,
            'kepada' => $data->kepada,
            'perihal' => $data->perihal,
            'lampiran' => $data->lampiran,
            'diteruskan_kepada' => $data->diteruskan_kepada,
            'tembusan' => $data->tembusan,
            'keterangan' => $data->keterangan,
            'data_dukung_path' => $data->data_dukung_path,
        ]);

        return $agendaSurat;
    }

    public function delete(AgendaSurat $agendaSurat): void
    {
        $agendaSurat->delete();
    }
}




