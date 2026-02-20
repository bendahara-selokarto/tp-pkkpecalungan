<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\DTOs;

class DataPemanfaatanTanahPekaranganHatinyaPkkData
{
    public function __construct(
        public string $kategori_pemanfaatan_lahan,
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
            $data['kategori_pemanfaatan_lahan'],
            $data['komoditi'],
            $data['jumlah_komoditi'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}



