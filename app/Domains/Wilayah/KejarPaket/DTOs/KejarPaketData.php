<?php

namespace App\Domains\Wilayah\KejarPaket\DTOs;

class KejarPaketData
{
    public function __construct(
        public string $nama_kejar_paket,
        public string $jenis_kejar_paket,
        public int $jumlah_warga_belajar_l,
        public int $jumlah_warga_belajar_p,
        public int $jumlah_pengajar_l,
        public int $jumlah_pengajar_p,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_kejar_paket'],
            $data['jenis_kejar_paket'],
            (int) $data['jumlah_warga_belajar_l'],
            (int) $data['jumlah_warga_belajar_p'],
            (int) $data['jumlah_pengajar_l'],
            (int) $data['jumlah_pengajar_p'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}





