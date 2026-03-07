<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Repositories;

use App\Domains\Wilayah\DataIndustriRumahTangga\DTOs\DataIndustriRumahTanggaData;
use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataIndustriRumahTanggaRepository implements DataIndustriRumahTanggaRepositoryInterface
{
    public function store(DataIndustriRumahTanggaData $data): DataIndustriRumahTangga
    {
        return DataIndustriRumahTangga::create([
            'kategori_jenis_industri' => $data->kategori_jenis_industri,
            'komoditi' => $data->komoditi,
            'jumlah_komoditi' => $data->jumlah_komoditi,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return DataIndustriRumahTangga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return DataIndustriRumahTangga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataIndustriRumahTangga
    {
        return DataIndustriRumahTangga::findOrFail($id);
    }

    public function update(DataIndustriRumahTangga $dataIndustriRumahTangga, DataIndustriRumahTanggaData $data): DataIndustriRumahTangga
    {
        $dataIndustriRumahTangga->update([
            'kategori_jenis_industri' => $data->kategori_jenis_industri,
            'komoditi' => $data->komoditi,
            'jumlah_komoditi' => $data->jumlah_komoditi,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $dataIndustriRumahTangga;
    }

    public function delete(DataIndustriRumahTangga $dataIndustriRumahTangga): void
    {
        $dataIndustriRumahTangga->delete();
    }
}
