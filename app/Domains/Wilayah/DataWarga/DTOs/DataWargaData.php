<?php

namespace App\Domains\Wilayah\DataWarga\DTOs;

class DataWargaData
{
    public function __construct(
        public string $dasawisma,
        public string $nama_kepala_keluarga,
        public string $alamat,
        public string $rt,
        public string $rw,
        public ?string $dusun,
        public ?string $alamat_detail,
        public int $jumlah_warga_laki_laki,
        public int $jumlah_warga_perempuan,
        public ?string $keterangan,
        public int $tahun_anggaran,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['dasawisma'],
            $data['nama_kepala_keluarga'],
            $data['alamat'],
            $data['rt'] ?? '',
            $data['rw'] ?? '',
            $data['dusun'] ?? null,
            $data['alamat_detail'] ?? null,
            (int) $data['jumlah_warga_laki_laki'],
            (int) $data['jumlah_warga_perempuan'],
            $data['keterangan'] ?? null,
            (int) $data['tahun_anggaran'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
