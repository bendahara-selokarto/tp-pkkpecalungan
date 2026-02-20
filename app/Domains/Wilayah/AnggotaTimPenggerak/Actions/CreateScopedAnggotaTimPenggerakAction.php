<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Actions;

use App\Domains\Wilayah\AnggotaTimPenggerak\DTOs\AnggotaTimPenggerakData;
use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\AnggotaTimPenggerak\Repositories\AnggotaTimPenggerakRepositoryInterface;
use App\Domains\Wilayah\AnggotaTimPenggerak\Services\AnggotaTimPenggerakScopeService;

class CreateScopedAnggotaTimPenggerakAction
{
    public function __construct(
        private readonly AnggotaTimPenggerakRepositoryInterface $anggotaTimPenggerakRepository,
        private readonly AnggotaTimPenggerakScopeService $anggotaTimPenggerakScopeService
    ) {
    }

    public function execute(array $payload, string $level): AnggotaTimPenggerak
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
            'level' => $level,
            'area_id' => $this->anggotaTimPenggerakScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->anggotaTimPenggerakRepository->store($data);
    }
}


