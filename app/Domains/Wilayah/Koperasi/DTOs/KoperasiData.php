<?php

namespace App\Domains\Wilayah\Koperasi\DTOs;

class KoperasiData
{
    public function __construct(
        public string $nama_koperasi,
        public string $jenis_usaha,
        public bool $berbadan_hukum,
        public bool $belum_berbadan_hukum,
        public int $jumlah_anggota_l,
        public int $jumlah_anggota_p,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_koperasi'],
            $data['jenis_usaha'],
            (bool) $data['berbadan_hukum'],
            (bool) $data['belum_berbadan_hukum'],
            (int) $data['jumlah_anggota_l'],
            (int) $data['jumlah_anggota_p'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}


