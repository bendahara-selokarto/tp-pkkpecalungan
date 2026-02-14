<?php

namespace App\Domains\Wilayah\Activities\DTOs;

class ActivityData
{
    public function __construct(
        public string $nama_kegiatan,
        public ?string $deskripsi,
        public string $tanggal,
        public ?int $desa_id,
        public ?int $kecamatan_id,
        public int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_kegiatan'],
            $data['deskripsi'] ?? null,
            $data['tanggal'],
            $data['desa_id'] ?? null,
            $data['kecamatan_id'] ?? null,
            $data['created_by'],
        );
    }
}
