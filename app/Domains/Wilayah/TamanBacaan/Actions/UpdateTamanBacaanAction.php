<?php

namespace App\Domains\Wilayah\TamanBacaan\Actions;

use App\Domains\Wilayah\TamanBacaan\DTOs\TamanBacaanData;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use App\Domains\Wilayah\TamanBacaan\Repositories\TamanBacaanRepositoryInterface;

class UpdateTamanBacaanAction
{
    public function __construct(
        private readonly TamanBacaanRepositoryInterface $tamanBacaanRepository
    ) {
    }

    public function execute(TamanBacaan $tamanBacaan, array $payload): TamanBacaan
    {
        $data = TamanBacaanData::fromArray([
            'nama_taman_bacaan' => $payload['nama_taman_bacaan'],
            'nama_pengelola' => $payload['nama_pengelola'],
            'jumlah_buku_bacaan' => $payload['jumlah_buku_bacaan'],
            'jenis_buku' => $payload['jenis_buku'],
            'kategori' => $payload['kategori'],
            'jumlah' => $payload['jumlah'],
            'level' => $tamanBacaan->level,
            'area_id' => $tamanBacaan->area_id,
            'created_by' => $tamanBacaan->created_by,
        ]);

        return $this->tamanBacaanRepository->update($tamanBacaan, $data);
    }
}


