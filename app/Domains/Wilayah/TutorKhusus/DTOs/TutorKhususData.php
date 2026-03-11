<?php

namespace App\Domains\Wilayah\TutorKhusus\DTOs;

class TutorKhususData
{
    public function __construct(
        public string $jenis_tutor,
        public int $jumlah_tutor,
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
            $data['jenis_tutor'],
            (int) $data['jumlah_tutor'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
