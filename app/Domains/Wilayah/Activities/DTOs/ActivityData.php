<?php

namespace App\Domains\Wilayah\Activities\DTOs;

class ActivityData
{
    public function __construct(
        public string $title,
        public ?string $nama_petugas,
        public ?string $jabatan_petugas,
        public ?string $description,
        public ?string $uraian,
        public string $level,
        public int $area_id,
        public int $created_by,
        public string $activity_date,
        public ?string $tempat_kegiatan,
        public string $status = 'draft',
        public ?string $tanda_tangan = null,
        public ?string $image_path = null,
        public ?string $document_path = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['nama_petugas'] ?? null,
            $data['jabatan_petugas'] ?? null,
            $data['description'] ?? null,
            $data['uraian'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
            $data['activity_date'],
            $data['tempat_kegiatan'] ?? null,
            $data['status'] ?? 'draft',
            $data['tanda_tangan'] ?? null,
            $data['image_path'] ?? null,
            $data['document_path'] ?? null,
        );
    }
}
