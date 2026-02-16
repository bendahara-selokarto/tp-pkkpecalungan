<?php

namespace App\Domains\Wilayah\AnggotaPokja\Actions;

use App\Domains\Wilayah\AnggotaPokja\DTOs\AnggotaPokjaData;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepository;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;

class CreateScopedAnggotaPokjaAction
{
    public function __construct(
        private readonly AnggotaPokjaRepository $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService
    ) {
    }

    public function execute(array $payload, string $level): AnggotaPokja
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
            'level' => $level,
            'area_id' => $this->anggotaPokjaScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->anggotaPokjaRepository->store($data);
    }
}
