<?php

namespace App\Domains\Wilayah\DataPelatihanKader\DTOs;

class DataPelatihanKaderData
{
    public function __construct(
        public string $nomor_registrasi,
        public string $nama_lengkap_kader,
        public string $tanggal_masuk_tp_pkk,
        public string $jabatan_fungsi,
        public int $nomor_urut_pelatihan,
        public string $judul_pelatihan,
        public string $jenis_kriteria_kaderisasi,
        public int $tahun_penyelenggaraan,
        public string $institusi_penyelenggara,
        public string $status_sertifikat,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nomor_registrasi'],
            $data['nama_lengkap_kader'],
            $data['tanggal_masuk_tp_pkk'],
            $data['jabatan_fungsi'],
            (int) $data['nomor_urut_pelatihan'],
            $data['judul_pelatihan'],
            $data['jenis_kriteria_kaderisasi'],
            (int) $data['tahun_penyelenggaraan'],
            $data['institusi_penyelenggara'],
            $data['status_sertifikat'],
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
