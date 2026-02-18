<?php

namespace App\Domains\Wilayah\AnggotaPokja\Actions;

use App\Domains\Wilayah\AnggotaPokja\DTOs\AnggotaPokjaData;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;

class UpdateAnggotaPokjaAction
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository
    ) {
    }

    public function execute(AnggotaPokja $anggotaPokja, array $payload): AnggotaPokja
    {
        $data = AnggotaPokjaData::fromArray([
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
            'pokja' => $payload['pokja'],
            'level' => $anggotaPokja->level,
            'area_id' => $anggotaPokja->area_id,
            'created_by' => $anggotaPokja->created_by,
        ]);

        return $this->anggotaPokjaRepository->update($anggotaPokja, $data);
    }
}

