<?php

namespace App\Domains\Wilayah\LiterasiWarga\DTOs;

class LiterasiWargaData
{
    public function __construct(
        public int $jumlah_tiga_buta,
        public ?string $keterangan,
        public int $tahun_anggaran,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['jumlah_tiga_buta'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
