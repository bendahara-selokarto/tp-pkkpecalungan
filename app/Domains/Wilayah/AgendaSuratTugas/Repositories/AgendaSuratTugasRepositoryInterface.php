<?php

namespace App\Domains\Wilayah\AgendaSuratTugas\Repositories;

use App\Domains\Wilayah\AgendaSuratTugas\DTOs\AgendaSuratTugasData;
use App\Domains\Wilayah\AgendaSuratTugas\Models\AgendaSuratTugas;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface AgendaSuratTugasRepositoryInterface
{
    public function listScoped(User $user, string $level, int $perPage): LengthAwarePaginator;
    public function findScoped(int $id, string $level, int $areaId): ?AgendaSuratTugas;
    public function create(AgendaSuratTugasData $data): AgendaSuratTugas;
    public function update(AgendaSuratTugas $model, array $data): bool;
    public function delete(AgendaSuratTugas $model): bool;
}
