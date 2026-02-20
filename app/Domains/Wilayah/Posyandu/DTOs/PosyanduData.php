<?php

namespace App\Domains\Wilayah\Posyandu\DTOs;

class PosyanduData
{
    public function __construct(
        public string $nama_posyandu,
        public string $nama_pengelola,
        public string $nama_sekretaris,
        public string $jenis_posyandu,
        public int $jumlah_kader,
        public string $jenis_kegiatan,
        public int $frekuensi_layanan,
        public int $jumlah_pengunjung_l,
        public int $jumlah_pengunjung_p,
        public int $jumlah_petugas_l,
        public int $jumlah_petugas_p,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_posyandu'],
            $data['nama_pengelola'],
            $data['nama_sekretaris'],
            $data['jenis_posyandu'],
            (int) $data['jumlah_kader'],
            $data['jenis_kegiatan'],
            (int) $data['frekuensi_layanan'],
            (int) $data['jumlah_pengunjung_l'],
            (int) $data['jumlah_pengunjung_p'],
            (int) $data['jumlah_petugas_l'],
            (int) $data['jumlah_petugas_p'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}





