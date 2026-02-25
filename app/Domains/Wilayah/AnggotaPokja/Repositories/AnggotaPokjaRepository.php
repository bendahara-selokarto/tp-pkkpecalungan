<?php

namespace App\Domains\Wilayah\AnggotaPokja\Repositories;

use App\Domains\Wilayah\AnggotaPokja\DTOs\AnggotaPokjaData;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AnggotaPokjaRepository implements AnggotaPokjaRepositoryInterface
{
    public function store(AnggotaPokjaData $data): AnggotaPokja
    {
        return AnggotaPokja::create([
            'nama' => $data->nama,
            'jabatan' => $data->jabatan,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'pekerjaan' => $data->pekerjaan,
            'keterangan' => $data->keterangan,
            'pokja' => $data->pokja,
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator
    {
        return AnggotaPokja::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return AnggotaPokja::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): AnggotaPokja
    {
        return AnggotaPokja::findOrFail($id);
    }

    public function update(AnggotaPokja $anggotaPokja, AnggotaPokjaData $data): AnggotaPokja
    {
        $anggotaPokja->update([
            'nama' => $data->nama,
            'jabatan' => $data->jabatan,
            'jenis_kelamin' => $data->jenis_kelamin,
            'tempat_lahir' => $data->tempat_lahir,
            'tanggal_lahir' => $data->tanggal_lahir,
            'status_perkawinan' => $data->status_perkawinan,
            'alamat' => $data->alamat,
            'pendidikan' => $data->pendidikan,
            'pekerjaan' => $data->pekerjaan,
            'keterangan' => $data->keterangan,
            'pokja' => $data->pokja,
        ]);

        return $anggotaPokja;
    }

    public function delete(AnggotaPokja $anggotaPokja): void
    {
        $anggotaPokja->delete();
    }
}
