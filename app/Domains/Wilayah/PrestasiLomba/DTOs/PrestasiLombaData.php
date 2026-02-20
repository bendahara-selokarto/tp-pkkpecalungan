<?php

namespace App\Domains\Wilayah\PrestasiLomba\DTOs;

class PrestasiLombaData
{
    public function __construct(
        public int $tahun,
        public string $jenis_lomba,
        public string $lokasi,
        public bool $prestasi_kecamatan,
        public bool $prestasi_kabupaten,
        public bool $prestasi_provinsi,
        public bool $prestasi_nasional,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['tahun'],
            $data['jenis_lomba'],
            $data['lokasi'],
            (bool) $data['prestasi_kecamatan'],
            (bool) $data['prestasi_kabupaten'],
            (bool) $data['prestasi_provinsi'],
            (bool) $data['prestasi_nasional'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
