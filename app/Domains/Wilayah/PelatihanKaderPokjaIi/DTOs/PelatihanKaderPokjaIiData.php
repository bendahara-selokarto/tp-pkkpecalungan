<?php

namespace App\Domains\Wilayah\PelatihanKaderPokjaIi\DTOs;

class PelatihanKaderPokjaIiData
{
    public function __construct(
        public string $kategori_pelatihan,
        public int $jumlah_kader,
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
            $data['kategori_pelatihan'],
            (int) $data['jumlah_kader'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
