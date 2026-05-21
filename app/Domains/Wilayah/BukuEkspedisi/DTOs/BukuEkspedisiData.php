<?php

namespace App\Domains\Wilayah\BukuEkspedisi\DTOs;

class BukuEkspedisiData
{
    public function __construct(
        public string $title,
        public string $file_path,
        public string $original_name,
        public ?string $mime_type,
        public string $extension,
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
            $data['title'],
            $data['file_path'],
            $data['original_name'],
            $data['mime_type'] ?? null,
            $data['extension'],
            $data['size_bytes'] ?? 0,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
            $data['tahun_anggaran']
        );
    }
}
