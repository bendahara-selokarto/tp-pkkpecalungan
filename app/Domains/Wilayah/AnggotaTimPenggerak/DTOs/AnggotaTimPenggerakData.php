<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\DTOs;

class AnggotaTimPenggerakData
{
    public function __construct(
        public string $nama,
        public string $jabatan,
        public string $jenis_kelamin,
        public string $tempat_lahir,
        public string $tanggal_lahir,
        public string $status_perkawinan,
        public string $alamat,
        public string $pendidikan,
        public string $pekerjaan,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nama'],
            $data['jabatan'],
            $data['jenis_kelamin'],
            $data['tempat_lahir'],
            $data['tanggal_lahir'],
            $data['status_perkawinan'],
            $data['alamat'],
            $data['pendidikan'],
            $data['pekerjaan'],
            $data['keterangan'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
        );
    }
}

