<?php

namespace App\Domains\Wilayah\Bantuan\DTOs;

class BantuanData
{
    public function __construct(
        public string $lokasi_penerima,
        public string $jenis_bantuan,
        public ?string $keterangan,
        public string $asal_bantuan,
        public string $jumlah,
        public string $tanggal,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['lokasi_penerima'],
            $data['jenis_bantuan'],
            $data['keterangan'] ?? null,
            $data['asal_bantuan'],
            (string) $data['jumlah'],
            $data['tanggal'],
            $data['level'],
            $data['area_id'],
            $data['created_by'],
        );
    }
}
