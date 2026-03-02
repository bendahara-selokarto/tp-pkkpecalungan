<?php

namespace App\Domains\Wilayah\AgendaSurat\DTOs;

class AgendaSuratData
{
    public function __construct(
        public string $jenis_surat,
        public ?string $tanggal_terima,
        public string $tanggal_surat,
        public string $nomor_surat,
        public ?string $asal_surat,
        public ?string $dari,
        public ?string $kepada,
        public string $perihal,
        public ?string $lampiran,
        public ?string $diteruskan_kepada,
        public ?string $tembusan,
        public ?string $keterangan,
        public ?string $data_dukung_path,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['jenis_surat'],
            $data['tanggal_terima'] ?? null,
            $data['tanggal_surat'],
            $data['nomor_surat'],
            $data['asal_surat'] ?? null,
            $data['dari'] ?? null,
            $data['kepada'] ?? null,
            $data['perihal'],
            $data['lampiran'] ?? null,
            $data['diteruskan_kepada'] ?? null,
            $data['tembusan'] ?? null,
            $data['keterangan'] ?? null,
            $data['data_dukung_path'] ?? null,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
        );
    }
}
