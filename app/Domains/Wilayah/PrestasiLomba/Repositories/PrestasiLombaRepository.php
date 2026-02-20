<?php

namespace App\Domains\Wilayah\PrestasiLomba\Repositories;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use Illuminate\Support\Collection;

class PrestasiLombaRepository implements PrestasiLombaRepositoryInterface
{
    public function store(PrestasiLombaData $data): PrestasiLomba
    {
        return PrestasiLomba::create([
            'tahun' => $data->tahun,
            'jenis_lomba' => $data->jenis_lomba,
            'lokasi' => $data->lokasi,
            'prestasi_kecamatan' => $data->prestasi_kecamatan,
            'prestasi_kabupaten' => $data->prestasi_kabupaten,
            'prestasi_provinsi' => $data->prestasi_provinsi,
            'prestasi_nasional' => $data->prestasi_nasional,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return PrestasiLomba::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): PrestasiLomba
    {
        return PrestasiLomba::findOrFail($id);
    }

    public function update(PrestasiLomba $prestasiLomba, PrestasiLombaData $data): PrestasiLomba
    {
        $prestasiLomba->update([
            'tahun' => $data->tahun,
            'jenis_lomba' => $data->jenis_lomba,
            'lokasi' => $data->lokasi,
            'prestasi_kecamatan' => $data->prestasi_kecamatan,
            'prestasi_kabupaten' => $data->prestasi_kabupaten,
            'prestasi_provinsi' => $data->prestasi_provinsi,
            'prestasi_nasional' => $data->prestasi_nasional,
            'keterangan' => $data->keterangan,
        ]);

        return $prestasiLomba;
    }

    public function delete(PrestasiLomba $prestasiLomba): void
    {
        $prestasiLomba->delete();
    }
}
