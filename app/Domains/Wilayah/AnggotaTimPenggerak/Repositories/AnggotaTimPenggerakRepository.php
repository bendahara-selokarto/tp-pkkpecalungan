<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Repositories;

use App\Domains\Wilayah\AnggotaTimPenggerak\DTOs\AnggotaTimPenggerakData;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use Illuminate\Support\Collection;

class AnggotaTimPenggerakRepository implements AnggotaTimPenggerakRepositoryInterface
{
    public function store(AnggotaTimPenggerakData $data): AnggotaTimPenggerak
    {
        return AnggotaTimPenggerak::create([
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
            'level' => $data->level,
            'area_id' => $data->area_id,
            'created_by' => $data->created_by,
        ]);
    }

    public function getByLevelAndArea(string $level, int $areaId): Collection
    {
        return AnggotaTimPenggerak::query()
            ->where('level', $level)
            ->where('area_id', $areaId)
            ->latest('id')
            ->get();
    }

    public function find(int $id): AnggotaTimPenggerak
    {
        return AnggotaTimPenggerak::findOrFail($id);
    }

    public function update(AnggotaTimPenggerak $anggotaTimPenggerak, AnggotaTimPenggerakData $data): AnggotaTimPenggerak
    {
        $anggotaTimPenggerak->update([
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
        ]);

        return $anggotaTimPenggerak;
    }

    public function delete(AnggotaTimPenggerak $anggotaTimPenggerak): void
    {
        $anggotaTimPenggerak->delete();
    }
}


