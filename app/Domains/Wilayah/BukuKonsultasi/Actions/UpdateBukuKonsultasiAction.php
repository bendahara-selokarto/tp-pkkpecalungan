<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Actions;

use App\Domains\Wilayah\BukuKonsultasi\DTOs\BukuKonsultasiData;
use App\Domains\Wilayah\BukuKonsultasi\Models\BukuKonsultasi;
use App\Domains\Wilayah\BukuKonsultasi\Repositories\BukuKonsultasiRepositoryInterface;

class UpdateBukuKonsultasiAction
{
    public function __construct(
        private readonly BukuKonsultasiRepositoryInterface $bukuKonsultasiRepository
    ) {
    }

    public function execute(BukuKonsultasi $bukuKonsultasi, array $payload): BukuKonsultasi
    {
        $tahunAnggaran = isset($payload['activity_date']) 
            ? (int) date('Y', strtotime($payload['activity_date']))
            : $bukuKonsultasi->tahun_anggaran;

        $data = BukuKonsultasiData::fromArray(array_merge($bukuKonsultasi->toArray(), $payload, [
            'tahun_anggaran' => $tahunAnggaran,
        ]));

        return $this->bukuKonsultasiRepository->update($bukuKonsultasi, $data);
    }
}
