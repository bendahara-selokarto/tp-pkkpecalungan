<?php

namespace App\Domains\Wilayah\DataWarga\DTOs;

class DataWargaData
{
    public function __construct(
        public string $dasawisma,
        public string $nama_kepala_keluarga,
        public string $alamat,
        public int $jumlah_warga_laki_laki,
        public int $jumlah_warga_perempuan,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['dasawisma'],
            $data['nama_kepala_keluarga'],
            $data['alamat'],
            (int) $data['jumlah_warga_laki_laki'],
            (int) $data['jumlah_warga_perempuan'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
