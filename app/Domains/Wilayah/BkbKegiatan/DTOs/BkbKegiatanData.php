<?php

namespace App\Domains\Wilayah\BkbKegiatan\DTOs;

class BkbKegiatanData
{
    public function __construct(
        public int $jumlah_kelompok,
        public int $jumlah_ibu_peserta,
        public int $jumlah_ape_set,
        public int $jumlah_kelompok_simulasi,
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
            (int) $data['jumlah_kelompok'],
            (int) $data['jumlah_ibu_peserta'],
            (int) $data['jumlah_ape_set'],
            (int) $data['jumlah_kelompok_simulasi'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
