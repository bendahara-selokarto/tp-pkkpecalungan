<?php

namespace App\Domains\Wilayah\TutorKhusus\Repositories;

use App\Domains\Wilayah\TutorKhusus\DTOs\TutorKhususData;
use App\Domains\Wilayah\TutorKhusus\Models\TutorKhusus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TutorKhususRepositoryInterface
{
    public function store(TutorKhususData $data): TutorKhusus;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function find(int $id): TutorKhusus;

    public function update(TutorKhusus $tutorKhusus, TutorKhususData $data): TutorKhusus;

    public function delete(TutorKhusus $tutorKhusus): void;
}
