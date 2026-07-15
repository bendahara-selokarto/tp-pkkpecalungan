<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\Repositories;

use App\Domains\Wilayah\DataKegiatanWarga\DTOs\DataKegiatanWargaData;
use App\Domains\Wilayah\DataKegiatanWarga\Models\DataKegiatanWarga;
use App\Domains\Wilayah\Models\Area;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DataKegiatanWargaRepository implements DataKegiatanWargaRepositoryInterface
{
    public function store(DataKegiatanWargaData $data): DataKegiatanWarga
    {
        return DataKegiatanWarga::create([
            'kegiatan' => $data->kegiatan,
            'aktivitas' => $data->aktivitas,
            'keterangan' => $data->keterangan,
            'is_pkg' => $data->is_pkg,
            'is_tbc' => $data->is_tbc,
            'source_module' => $data->source_module,
            'source_id' => $data->source_id,
            'tahun_anggaran' => $data->tahun_anggaran,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator
    {
        return DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection
    {
        return DataKegiatanWarga::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->latest('id')
            ->get();
    }

    public function find(int $id): DataKegiatanWarga
    {
        return DataKegiatanWarga::findOrFail($id);
    }

    public function update(DataKegiatanWarga $dataKegiatanWarga, DataKegiatanWargaData $data): DataKegiatanWarga
    {
        $dataKegiatanWarga->update([
            'kegiatan' => $data->kegiatan,
            'aktivitas' => $data->aktivitas,
            'keterangan' => $data->keterangan,
            'is_pkg' => $data->is_pkg,
            'is_tbc' => $data->is_tbc,
            'source_module' => $data->source_module,
            'source_id' => $data->source_id,
            'tahun_anggaran' => $data->tahun_anggaran,
        ]);

        return $dataKegiatanWarga;
    }

    public function delete(DataKegiatanWarga $dataKegiatanWarga): void
    {
        $dataKegiatanWarga->delete();
    }

    public function getRecapByDesaForKecamatan(int $kecamatanAreaId, int $tahunAnggaran): Collection
    {
        $desaAreas = Area::query()
            ->where('parent_id', $kecamatanAreaId)
            ->where('level', 'desa')
            ->orderBy('code')
            ->get(['id', 'name', 'code']);

        $desaIds = $desaAreas->pluck('id')->toArray();

        $dataKegiatans = DataKegiatanWarga::query()
            ->where('level', 'desa')
            ->whereIn('area_id', $desaIds)
            ->where('tahun_anggaran', $tahunAnggaran)
            ->get();

        $groupedData = $dataKegiatans->groupBy('area_id');

        $kegiatanOptions = DataKegiatanWarga::kegiatanOptions();

        return $desaAreas->map(function (Area $desa) use ($groupedData, $kegiatanOptions) {
            $desaData = $groupedData->get($desa->id, collect());
            
            $activities = [];
            foreach ($kegiatanOptions as $kegiatan) {
                $item = $desaData->firstWhere('kegiatan', $kegiatan);
                $activities[] = [
                    'kegiatan' => $kegiatan,
                    'aktivitas' => $item?->aktivitas ?? false,
                    'keterangan' => $item?->keterangan ?? '-',
                ];
            }

            return [
                'area_id' => $desa->id,
                'nama_desa' => $desa->name,
                'kode_desa' => $desa->code,
                'activities' => $activities,
            ];
        });
    }
}
