<?php

namespace App\Domains\Wilayah\KaderKhusus\Actions;

use App\Domains\Wilayah\KaderKhusus\DTOs\KaderKhususData;
use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\KaderKhusus\Repositories\KaderKhususRepositoryInterface;

class UpdateKaderKhususAction
{
    public function __construct(
        private readonly KaderKhususRepositoryInterface $kaderKhususRepository
    ) {
    }

    public function execute(KaderKhusus $kaderKhusus, array $payload): KaderKhusus
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
            'level' => $kaderKhusus->level,
            'area_id' => $kaderKhusus->area_id,
            'created_by' => $kaderKhusus->created_by,
        ]);

        return $this->kaderKhususRepository->update($kaderKhusus, $data);
    }
}
