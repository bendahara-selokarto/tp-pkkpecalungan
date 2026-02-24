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
            ...$payload,
            'level' => $level,
            'area_id' => $this->programPrioritasScopeService->requireUserAreaId(),
            'created_by' => auth()->id(),
        ]);

        return $this->programPrioritasRepository->store($data);
    }
}
