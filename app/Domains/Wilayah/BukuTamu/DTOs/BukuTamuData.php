<?php

namespace App\Domains\Wilayah\BukuTamu\DTOs;

class BukuTamuData
{
    public function __construct(
        public ?string $visit_date,
        public ?string $description,
        public ?string $file_path,
        public ?string $original_name,
        public ?string $mime_type,
        public ?string $extension,
        public int $size_bytes,
        public string $level,
        public int $area_id,
        public int $created_by,
        public int $tahun_anggaran,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['visit_date'] ?? null,
            $data['description'] ?? null,
            $data['file_path'] ?? null,
            $data['original_name'] ?? null,
            $data['mime_type'] ?? null,
            $data['extension'] ?? null,
            $data['size_bytes'] ?? 0,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
            $data['tahun_anggaran']
        );
    }
}
