<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\DTOs;

class AgendaSuratTugasData
{
    public function __construct(
        public string $nomor_surat,
        public string $tanggal_surat,
        public string $kepada,
        public string $perihal,
        public ?string $lampiran,
        public ?string $tembusan,
        public ?string $file_path,
        public ?string $original_name,
        public ?string $mime_type,
        public ?string $extension,
        public int $size_bytes,
        public string $level,
        public int $area_id,
        public int $created_by,
        public int $tahun_anggaran,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['nomor_surat'],
            $data['tanggal_surat'],
            $data['kepada'],
            $data['perihal'],
            $data['lampiran'] ?? null,
            $data['tembusan'] ?? null,
            $data['file_path'] ?? null,
            $data['original_name'] ?? null,
            $data['mime_type'] ?? null,
            $data['extension'] ?? null,
            $data['size_bytes'] ?? 0,
            $data['level'],
            $data['area_id'],
            $data['created_by'],
            $data['tahun_anggaran']
        );
    }
}
