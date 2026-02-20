<?php

namespace App\Domains\Wilayah\DataKeluarga\DTOs;

class DataKeluargaData
{
    public function __construct(
        public string $kategori_keluarga,
        public int $jumlah_keluarga,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['kategori_keluarga'],
            (int) $data['jumlah_keluarga'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}

