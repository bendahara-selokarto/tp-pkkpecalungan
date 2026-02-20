<?php

namespace App\Domains\Wilayah\Inventaris\DTOs;

class InventarisData
{
    public function __construct(
        public string $name,
        public ?string $asal_barang,
        public ?string $description,
        public ?string $keterangan,
        public int $quantity,
        public string $unit,
        public ?string $tanggal_penerimaan,
        public ?string $tempat_penyimpanan,
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
            $data['asal_barang'] ?? null,
            $data['description'] ?? null,
            $data['keterangan'] ?? null,
            $data['quantity'],
            $data['unit'],
            $data['tanggal_penerimaan'] ?? null,
            $data['tempat_penyimpanan'] ?? null,
            $data['condition'],
            $data['level'],
            $data['area_id'],
            $data['created_by']
        );
    }
}
