<?php

namespace App\Domains\Wilayah\KejarPaket\Actions;

use App\Domains\Wilayah\KejarPaket\DTOs\KejarPaketData;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Domains\Wilayah\KejarPaket\Repositories\KejarPaketRepositoryInterface;

class UpdateKejarPaketAction
{
    public function __construct(
        private readonly KejarPaketRepositoryInterface $kejarPaketRepository
    ) {
    }

    public function execute(KejarPaket $kejarPaket, array $payload): KejarPaket
    {
        $data = KejarPaketData::fromArray([
            'nama_kejar_paket' => $payload['nama_kejar_paket'],
            'jenis_kejar_paket' => $payload['jenis_kejar_paket'],
            'jumlah_warga_belajar_l' => $payload['jumlah_warga_belajar_l'],
            'jumlah_warga_belajar_p' => $payload['jumlah_warga_belajar_p'],
            'jumlah_pengajar_l' => $payload['jumlah_pengajar_l'],
            'jumlah_pengajar_p' => $payload['jumlah_pengajar_p'],
            'level' => $kejarPaket->level,
            'area_id' => $kejarPaket->area_id,
            'created_by' => $kejarPaket->created_by,
        ]);

        return $this->kejarPaketRepository->update($kejarPaket, $data);
    }
}





