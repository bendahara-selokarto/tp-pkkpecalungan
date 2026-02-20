<?php

namespace App\Domains\Wilayah\WarungPkk\DTOs;

class WarungPkkData
{
    public function __construct(
        public string $nama_warung_pkk,
        public string $nama_pengelola,
        public string $komoditi,
        public string $kategori,
        public string $volume,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_warung_pkk'],
            $data['nama_pengelola'],
            $data['komoditi'],
            $data['kategori'],
            $data['volume'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
