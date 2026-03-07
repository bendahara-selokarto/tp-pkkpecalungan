<?php

namespace App\Domains\Wilayah\Paar\DTOs;

class PaarData
{
    public function __construct(
        public string $indikator,
        public int $jumlah,
        public ?string $keterangan,
        public int $tahun_anggaran,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['indikator'],
            (int) $data['jumlah'],
            isset($data['keterangan']) ? (string) $data['keterangan'] : null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
