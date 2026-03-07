<?php

namespace App\Domains\Wilayah\BukuKeuangan\DTOs;

class BukuKeuanganData
{
    public function __construct(
        public string $transaction_date,
        public string $source,
        public string $description,
        public ?string $reference_number,
        public string $entry_type,
        public string $amount,
        public int $tahun_anggaran,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['transaction_date'],
            $data['source'],
            $data['description'],
            $data['reference_number'] ?? null,
            $data['entry_type'],
            (string) $data['amount'],
            (int) $data['tahun_anggaran'],
            $data['level'],
            $data['area_id'],
            $data['created_by'],
        );
    }
}
