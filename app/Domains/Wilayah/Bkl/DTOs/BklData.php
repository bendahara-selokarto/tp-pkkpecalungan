<?php

namespace App\Domains\Wilayah\Bkl\DTOs;

class BklData
{
    public function __construct(
        public string $desa,
        public string $nama_bkl,
        public string $no_tgl_sk,
        public string $nama_ketua_kelompok,
        public int $jumlah_anggota,
        public string $kegiatan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['desa'],
            $data['nama_bkl'],
            $data['no_tgl_sk'],
            $data['nama_ketua_kelompok'],
            (int) $data['jumlah_anggota'],
            $data['kegiatan'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
