<?php

namespace App\Domains\Wilayah\DataKegiatanWarga\DTOs;

class DataKegiatanWargaData
{
    public function __construct(
        public string $kegiatan,
        public bool $aktivitas,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['kegiatan'],
            (bool) $data['aktivitas'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
