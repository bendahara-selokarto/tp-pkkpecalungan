<?php

namespace App\Domains\Wilayah\PrestasiLomba\Repositories;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator
    {
        return PrestasiLomba::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection
    {
        return PrestasiLomba::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->when(is_int($creatorIdFilter), static fn ($query) => $query->where('created_by', $creatorIdFilter))
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




