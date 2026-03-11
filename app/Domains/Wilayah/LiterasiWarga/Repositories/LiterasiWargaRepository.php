<?php

namespace App\Domains\Wilayah\LiterasiWarga\Repositories;

use App\Domains\Wilayah\LiterasiWarga\DTOs\LiterasiWargaData;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LiterasiWargaRepository implements LiterasiWargaRepositoryInterface
{
    public function store(LiterasiWargaData $data): LiterasiWarga
    {
        return LiterasiWarga::create([
            'jumlah_tiga_buta' => $data->jumlah_tiga_buta,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return LiterasiWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return LiterasiWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): LiterasiWarga
    {
        return LiterasiWarga::findOrFail($id);
    }

    public function update(LiterasiWarga $literasiWarga, LiterasiWargaData $data): LiterasiWarga
    {
        $literasiWarga->update([
            'jumlah_tiga_buta' => $data->jumlah_tiga_buta,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $literasiWarga;
    }

    public function delete(LiterasiWarga $literasiWarga): void
    {
        $literasiWarga->delete();
    }
}
