<?php

namespace App\Domains\Wilayah\BukuKonsultasi\DTOs;

class BukuKonsultasiData
{
    public function __construct(
        public string $activity_date,
        public string $description,
        public ?string $disposition,
        public string $level,
        public int $area_id,
        public string $group,
        public int $created_by,
        public ?int $tahun_anggaran = null
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['activity_date'],
            $data['description'],
            $data['disposition'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            $data['group'],
            (int) $data['created_by'],
            isset($data['tahun_anggaran']) ? (int) $data['tahun_anggaran'] : null
        );
    }
}
