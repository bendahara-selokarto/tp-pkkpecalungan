<?php

namespace App\Domains\Wilayah\ProgramPrioritas\DTOs;

class ProgramPrioritasData
{
    public function __construct(
        public string $program,
        public string $prioritas_program,
        public string $kegiatan,
        public string $sasaran_target,
        public bool $jadwal_bulan_1,
        public bool $jadwal_bulan_2,
        public bool $jadwal_bulan_3,
        public bool $jadwal_bulan_4,
        public bool $jadwal_bulan_5,
        public bool $jadwal_bulan_6,
        public bool $jadwal_bulan_7,
        public bool $jadwal_bulan_8,
        public bool $jadwal_bulan_9,
        public bool $jadwal_bulan_10,
        public bool $jadwal_bulan_11,
        public bool $jadwal_bulan_12,
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
        $monthlyFlags = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyFlags[$month] = (bool) ($data["jadwal_bulan_{$month}"] ?? false);
        }

        $quarterFlags = self::buildQuarterFlagsFromMonthly($monthlyFlags);

        return new self(
            $data['program'],
            $data['prioritas_program'],
            $data['kegiatan'],
            $data['sasaran_target'],
            $monthlyFlags[1],
            $monthlyFlags[2],
            $monthlyFlags[3],
            $monthlyFlags[4],
            $monthlyFlags[5],
            $monthlyFlags[6],
            $monthlyFlags[7],
            $monthlyFlags[8],
            $monthlyFlags[9],
            $monthlyFlags[10],
            $monthlyFlags[11],
            $monthlyFlags[12],
            $quarterFlags['jadwal_i'],
            $quarterFlags['jadwal_ii'],
            $quarterFlags['jadwal_iii'],
            $quarterFlags['jadwal_iv'],
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

    /**
     * @param  array<int, bool>  $monthlyFlags
     * @return array{jadwal_i: bool, jadwal_ii: bool, jadwal_iii: bool, jadwal_iv: bool}
     */
    private static function buildQuarterFlagsFromMonthly(array $monthlyFlags): array
    {
        return [
            'jadwal_i' => ($monthlyFlags[1] ?? false) || ($monthlyFlags[2] ?? false) || ($monthlyFlags[3] ?? false),
            'jadwal_ii' => ($monthlyFlags[4] ?? false) || ($monthlyFlags[5] ?? false) || ($monthlyFlags[6] ?? false),
            'jadwal_iii' => ($monthlyFlags[7] ?? false) || ($monthlyFlags[8] ?? false) || ($monthlyFlags[9] ?? false),
            'jadwal_iv' => ($monthlyFlags[10] ?? false) || ($monthlyFlags[11] ?? false) || ($monthlyFlags[12] ?? false),
        ];
    }
}
