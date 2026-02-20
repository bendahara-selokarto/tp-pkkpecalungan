<?php

namespace App\Domains\Wilayah\TamanBacaan\DTOs;

class TamanBacaanData
{
    public function __construct(
        public string $nama_taman_bacaan,
        public string $nama_pengelola,
        public string $jumlah_buku_bacaan,
        public string $jenis_buku,
        public string $kategori,
        public string $jumlah,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_taman_bacaan'],
            $data['nama_pengelola'],
            $data['jumlah_buku_bacaan'],
            $data['jenis_buku'],
            $data['kategori'],
            $data['jumlah'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}


