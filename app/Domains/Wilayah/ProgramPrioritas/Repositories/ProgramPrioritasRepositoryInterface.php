<?php

namespace App\Domains\Wilayah\ProgramPrioritas\Repositories;

use App\Domains\Wilayah\ProgramPrioritas\DTOs\ProgramPrioritasData;
use App\Domains\Wilayah\ProgramPrioritas\Models\ProgramPrioritas;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProgramPrioritasRepositoryInterface
{
    public function store(ProgramPrioritasData $data): ProgramPrioritas;

    public function paginateByLevelAndArea(
        string $level,
        int $areaId,
        int $perPage,
        ?int $creatorIdFilter = null
    ): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): ProgramPrioritas;

    public function update(ProgramPrioritas $programPrioritas, ProgramPrioritasData $data): ProgramPrioritas;

    public function delete(ProgramPrioritas $programPrioritas): void;
}
