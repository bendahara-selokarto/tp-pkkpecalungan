<?php

namespace App\Domains\Wilayah\BukuTamu\DTOs;

class BukuTamuData
{
    public function __construct(
        public string $visit_date,
        public string $guest_name,
        public string $purpose,
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
            $data['visit_date'],
            $data['guest_name'],
            $data['purpose'],
            $data['institution'] ?? null,
            $data['description'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by']
        );
    }
}
