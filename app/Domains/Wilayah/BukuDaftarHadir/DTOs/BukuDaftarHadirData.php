<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\DTOs;

class BukuDaftarHadirData
{
    public function __construct(
        public ?string $title,
        public ?string $attendance_date,
        public ?int $activity_id,
        public ?string $attendee_name,
        public ?string $institution,
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
            $data['title'] ?? null,
            $data['attendance_date'] ?? null,
            $data['activity_id'] ?? null,
            $data['attendee_name'] ?? null,
            $data['institution'] ?? null,
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
