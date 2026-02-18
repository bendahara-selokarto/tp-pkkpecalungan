<?php

namespace App\Domains\Wilayah\Bantuan\DTOs;

class BantuanData
{
    public function __construct(
        public string $name,
        public string $category,
        public ?string $description,
        public string $source,
        public string $amount,
        public string $received_date,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['category'],
            $data['description'] ?? null,
            $data['source'],
            (string) $data['amount'],
            $data['received_date'],
            $data['level'],
            $data['area_id'],
            $data['created_by'],
        );
    }
}
