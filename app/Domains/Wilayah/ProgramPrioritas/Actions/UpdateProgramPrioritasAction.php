<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Actions;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;

class UpdateProgramPrioritasAction
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository
    ) {
    }

    public function execute(ProgramPrioritas $programPrioritas, array $payload): ProgramPrioritas
    {
        $data = ProgramPrioritasData::fromArray([
            'program' => $payload['program'],
            'prioritas_program' => $payload['prioritas_program'],
            'kegiatan' => $payload['kegiatan'],
            'sasaran_target' => $payload['sasaran_target'],
            'jadwal_i' => $payload['jadwal_i'],
            'jadwal_ii' => $payload['jadwal_ii'],
            'jadwal_iii' => $payload['jadwal_iii'],
            'jadwal_iv' => $payload['jadwal_iv'],
            'sumber_dana_pusat' => $payload['sumber_dana_pusat'],
            'sumber_dana_apbd' => $payload['sumber_dana_apbd'],
            'sumber_dana_swd' => $payload['sumber_dana_swd'],
            'sumber_dana_bant' => $payload['sumber_dana_bant'],
            'keterangan' => $payload['keterangan'] ?? null,
            'level' => $programPrioritas->level,
            'area_id' => $programPrioritas->area_id,
            'created_by' => $programPrioritas->created_by,
        ]);

        return $this->programPrioritasRepository->update($programPrioritas, $data);
    }
}
