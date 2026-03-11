<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\DTOs;

class PraKoperasiUp2kData
{
    public function __construct(
        public string $tingkat,
        public int $jumlah_kelompok,
        public int $jumlah_peserta,
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
            $data['tingkat'],
            (int) $data['jumlah_kelompok'],
            (int) $data['jumlah_peserta'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
