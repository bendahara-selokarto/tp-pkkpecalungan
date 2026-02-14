<?php

namespace App\Domains\Wilayah\Activities\DTOs;

class ActivityData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public string $level,
        public int $area_id,
        public int $created_by,
        public string $activity_date,
        public string $status = 'draft',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
            $data['activity_date'],
            $data['status'] ?? 'draft',
        );
    }
}
