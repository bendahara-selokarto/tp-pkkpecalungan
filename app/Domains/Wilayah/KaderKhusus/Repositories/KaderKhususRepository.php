<?php

namespace App\Domains\Wilayah\KaderKhusus\Repositories;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class KaderKhususRepository implements KaderKhususRepositoryInterface
{
    public function store(KaderKhususData $data): KaderKhusus
    {
        return KaderKhusus::create([
            'nama' => $data->nama,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'jenis_kader_khusus' => $data->jenis_kader_khusus,
            'keterangan' => $data->keterangan,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return KaderKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return KaderKhusus::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): KaderKhusus
    {
        return KaderKhusus::findOrFail($id);
    }

    public function update(KaderKhusus $kaderKhusus, KaderKhususData $data): KaderKhusus
    {
        $kaderKhusus->update([
            'nama' => $data->nama,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'jenis_kader_khusus' => $data->jenis_kader_khusus,
            'keterangan' => $data->keterangan,
        ]);

        return $kaderKhusus;
    }

    public function delete(KaderKhusus $kaderKhusus): void
    {
        $kaderKhusus->delete();
    }
}
