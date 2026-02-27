<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\DTOs;

class BukuDaftarHadirData
{
    public function __construct(
        public string $attendance_date,
        public int $activity_id,
        public string $attendee_name,
        public ?string $institution,
        public ?string $description,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['attendance_date'],
            $data['activity_id'],
            $data['attendee_name'],
            $data['institution'] ?? null,
            $data['description'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by']
        );
    }
}
