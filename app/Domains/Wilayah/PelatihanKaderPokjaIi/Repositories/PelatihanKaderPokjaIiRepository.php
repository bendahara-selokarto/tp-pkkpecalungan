<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\Repositories;

use App\Domains\Wilayah\PelatihanKaderPokjaIi\DTOs\PelatihanKaderPokjaIiData;
use App\Domains\Wilayah\PelatihanKaderPokjaIi\Models\PelatihanKaderPokjaIi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PelatihanKaderPokjaIiRepository implements PelatihanKaderPokjaIiRepositoryInterface
{
    public function store(PelatihanKaderPokjaIiData $data): PelatihanKaderPokjaIi
    {
        return PelatihanKaderPokjaIi::create([
            'kategori_pelatihan' => $data->kategori_pelatihan,
            'jumlah_kader' => $data->jumlah_kader,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return PelatihanKaderPokjaIi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return PelatihanKaderPokjaIi::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): PelatihanKaderPokjaIi
    {
        return PelatihanKaderPokjaIi::findOrFail($id);
    }

    public function update(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi, PelatihanKaderPokjaIiData $data): PelatihanKaderPokjaIi
    {
        $pelatihanKaderPokjaIi->update([
            'kategori_pelatihan' => $data->kategori_pelatihan,
            'jumlah_kader' => $data->jumlah_kader,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $pelatihanKaderPokjaIi;
    }

    public function delete(PelatihanKaderPokjaIi $pelatihanKaderPokjaIi): void
    {
        $pelatihanKaderPokjaIi->delete();
    }
}
