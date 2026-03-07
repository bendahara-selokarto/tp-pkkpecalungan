<?php

namespace App\Domains\Wilayah\AnggotaPokja\Actions;

use App\Domains\Wilayah\AnggotaPokja\DTOs\AnggotaPokjaData;
use App\Domains\Wilayah\AnggotaPokja\Models\AnggotaPokja;
use App\Domains\Wilayah\AnggotaPokja\Repositories\AnggotaPokjaRepositoryInterface;
use App\Domains\Wilayah\AnggotaPokja\Services\AnggotaPokjaScopeService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;

class CreateScopedAnggotaPokjaAction
{
    public function __construct(
        private readonly AnggotaPokjaRepositoryInterface $anggotaPokjaRepository,
        private readonly AnggotaPokjaScopeService $anggotaPokjaScopeService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

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
            'tahun_anggaran' => $this->activeBudgetYearContextService->requireForAuthenticatedUser(),
            'level' => $level,
            'area_id' => $this->anggotaPokjaScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->anggotaPokjaRepository->store($data);
    }
}
