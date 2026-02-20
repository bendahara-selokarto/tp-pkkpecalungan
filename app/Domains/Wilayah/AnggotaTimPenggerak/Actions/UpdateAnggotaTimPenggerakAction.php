<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Actions;

use App\Domains\Wilayah\AnggotaTimPenggerak\DTOs\AnggotaTimPenggerakData;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;

class UpdateAnggotaTimPenggerakAction
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository
    ) {
    }

    public function execute(AnggotaTimPenggerak $anggotaTimPenggerak, array $payload): AnggotaTimPenggerak
    {
        $data = AnggotaTimPenggerakData::fromArray([
            'nama' => $payload['nama'],
            'jabatan' => $payload['jabatan'],
            'jenis_kelamin' => $payload['jenis_kelamin'],
            'tempat_lahir' => $payload['tempat_lahir'],
            'tanggal_lahir' => $payload['tanggal_lahir'],
            'status_perkawinan' => $payload['status_perkawinan'],
            'alamat' => $payload['alamat'],
            'pendidikan' => $payload['pendidikan'],
            'pekerjaan' => $payload['pekerjaan'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $anggotaTimPenggerak->level,
            'area_id' => $anggotaTimPenggerak->area_id,
            'created_by' => $anggotaTimPenggerak->created_by,
        ]);

        return $this->anggotaTimPenggerakRepository->update($anggotaTimPenggerak, $data);
    }
}


