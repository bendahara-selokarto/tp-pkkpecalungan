<?php

namespace App\Domains\Wilayah\ProgramPrioritas\DTOs;

class ProgramPrioritasData
{
    public function __construct(
        public string $program,
        public string $prioritas_program,
        public string $kegiatan,
        public string $sasaran_target,
        public bool $jadwal_i,
        public bool $jadwal_ii,
        public bool $jadwal_iii,
        public bool $jadwal_iv,
        public bool $sumber_dana_pusat,
        public bool $sumber_dana_apbd,
        public bool $sumber_dana_swd,
        public bool $sumber_dana_bant,
        public ?string $keterangan,
        public string $level,
        public int $area_id,
        public int $created_by,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['program'],
            $data['prioritas_program'],
            $data['kegiatan'],
            $data['sasaran_target'],
            (bool) $data['jadwal_i'],
            (bool) $data['jadwal_ii'],
            (bool) $data['jadwal_iii'],
            (bool) $data['jadwal_iv'],
            (bool) $data['sumber_dana_pusat'],
            (bool) $data['sumber_dana_apbd'],
            (bool) $data['sumber_dana_swd'],
            (bool) $data['sumber_dana_bant'],
            $data['keterangan'] ?? null,
            $data['level'],
            (int) $data['area_id'],
            (int) $data['created_by'],
        );
    }
}
