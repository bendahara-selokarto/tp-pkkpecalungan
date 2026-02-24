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
            ...$payload,
            'level' => $programPrioritas->level,
            'area_id' => $programPrioritas->area_id,
            'created_by' => $programPrioritas->created_by,
        ]);

        return $this->programPrioritasRepository->update($programPrioritas, $data);
    }
}
