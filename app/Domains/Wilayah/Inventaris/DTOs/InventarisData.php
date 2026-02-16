<?php

namespace App\Domains\Wilayah\Inventaris\DTOs;

class InventarisData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public int $quantity,
        public string $unit,
        public string $condition,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['description'] ?? null,
            $data['quantity'],
            $data['unit'],
            $data['condition'],
            $data['level'],
            $data['area_id'],
            $data['created_by']
        );
    }
}
