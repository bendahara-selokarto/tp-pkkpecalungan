<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\DTOs;

class BukuNotulenRapatData
{
    public function __construct(
        public string $entry_date,
        public string $title,
        public ?string $person_name,
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
            $data['entry_date'],
            $data['title'],
            $data['person_name'] ?? null,
            $data['institution'] ?? null,
            $data['description'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by']
        );
    }
}
