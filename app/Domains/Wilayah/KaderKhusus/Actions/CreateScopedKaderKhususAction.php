<?php

namespace App\Domains\Wilayah\KaderKhusus\Actions;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;
use App\Domains\Wilayah\KaderKhusus\Services\KaderKhususScopeService;

class CreateScopedKaderKhususAction
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository,
        private readonly KaderKhususScopeService $kaderKhususScopeService
    ) {
    }

    public function execute(array $payload, string $level): KaderKhusus
    {
        $data = KaderKhususData::fromArray([
            'nama' => $payload['nama'],
            'jenis_kelamin' => $payload['jenis_kelamin'],
            'tempat_lahir' => $payload['tempat_lahir'],
            'tanggal_lahir' => $payload['tanggal_lahir'],
            'status_perkawinan' => $payload['status_perkawinan'],
            'alamat' => $payload['alamat'],
            'pendidikan' => $payload['pendidikan'],
            'jenis_kader_khusus' => $payload['jenis_kader_khusus'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $level,
            'area_id' => $this->kaderKhususScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->kaderKhususRepository->store($data);
    }
}
