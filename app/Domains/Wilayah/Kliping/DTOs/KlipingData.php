<?php

namespace App\Domains\Wilayah\Kliping\DTOs;

class KlipingData
{
    public function __construct(
        public string $date,
        public ?string $description,
        public string $file_path,
        public string $original_name,
        public string $mime_type,
        public string $extension,
        public int $size_bytes,
        public string $level,
        public int $area_id,
        public int $created_by,
        public int $tahun_anggaran
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['date'],
            $data['description'] ?? null,
            $data['file_path'],
            $data['original_name'],
            $data['mime_type'],
            $data['extension'],
            (int) $data['size_bytes'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
            (int) $data['tahun_anggaran']
        );
    }
}
