<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Actions;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use App\Domains\Wilayah\ProgramPrioritas\Repositories\ProgramPrioritasRepositoryInterface;
use App\Domains\Wilayah\ProgramPrioritas\Services\ProgramPrioritasScopeService;

class CreateScopedProgramPrioritasAction
{
    public function __construct(
        private readonly ProgramPrioritasRepositoryInterface $programPrioritasRepository,
        private readonly ProgramPrioritasScopeService $programPrioritasScopeService
    ) {
    }

    public function execute(array $payload, string $level): ProgramPrioritas
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
            'level' => $level,
            'area_id' => $this->programPrioritasScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->programPrioritasRepository->store($data);
    }
}
