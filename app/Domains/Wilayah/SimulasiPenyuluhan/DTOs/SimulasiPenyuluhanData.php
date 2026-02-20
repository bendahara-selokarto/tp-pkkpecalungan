<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\DTOs;

class SimulasiPenyuluhanData
{
    public function __construct(
        public string $nama_kegiatan,
        public string $jenis_simulasi_penyuluhan,
        public int $jumlah_kelompok,
        public int $jumlah_sosialisasi,
        public int $jumlah_kader_l,
        public int $jumlah_kader_p,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama_kegiatan'],
            $data['jenis_simulasi_penyuluhan'],
            (int) $data['jumlah_kelompok'],
            (int) $data['jumlah_sosialisasi'],
            (int) $data['jumlah_kader_l'],
            (int) $data['jumlah_kader_p'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
