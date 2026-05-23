<?php

namespace App\Domains\Wilayah\FotoKegiatan\DTOs;

use Illuminate\Support\Carbon;

class FotoKegiatanData
{
    public function __construct(
        public string $activity_date,
        public string $title,
        public ?string $description,
        public ?string $image_path,
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
            $data['title'],
            $data['description'] ?? null,
            $data['image_path'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            $data['group'],
            (int) $data['created_by'],
            isset($data['tahun_anggaran']) ? (int) $data['tahun_anggaran'] : null
        );
    }
}
