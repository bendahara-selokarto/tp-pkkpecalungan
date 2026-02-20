<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\DTOs;

class DataIndustriRumahTanggaData
{
    public function __construct(
        public string $kategori_jenis_industri,
        public string $komoditi,
        public string $jumlah_komoditi,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['kategori_jenis_industri'],
            $data['komoditi'],
            $data['jumlah_komoditi'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}




