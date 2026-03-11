<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Repositories;

use App\Domains\Wilayah\PraKoperasiUp2k\DTOs\PraKoperasiUp2kData;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PraKoperasiUp2kRepository implements PraKoperasiUp2kRepositoryInterface
{
    public function store(PraKoperasiUp2kData $data): PraKoperasiUp2k
    {
        return PraKoperasiUp2k::create([
            'tingkat' => $data->tingkat,
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_peserta' => $data->jumlah_peserta,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return PraKoperasiUp2k::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return PraKoperasiUp2k::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): PraKoperasiUp2k
    {
        return PraKoperasiUp2k::findOrFail($id);
    }

    public function update(PraKoperasiUp2k $praKoperasiUp2k, PraKoperasiUp2kData $data): PraKoperasiUp2k
    {
        $praKoperasiUp2k->update([
            'tingkat' => $data->tingkat,
            'jumlah_kelompok' => $data->jumlah_kelompok,
            'jumlah_peserta' => $data->jumlah_peserta,
            'keterangan' => $data->keterangan,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $praKoperasiUp2k;
    }

    public function delete(PraKoperasiUp2k $praKoperasiUp2k): void
    {
        $praKoperasiUp2k->delete();
    }
}
