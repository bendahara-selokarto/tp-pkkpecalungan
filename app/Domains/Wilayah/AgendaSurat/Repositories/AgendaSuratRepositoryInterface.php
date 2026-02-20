<?php

namespace App\Domains\Wilayah\AgendaSurat\Repositories;

use App\Domains\Wilayah\AgendaSurat\DTOs\AgendaSuratData;
use App\Domains\Wilayah\AgendaSurat\Models\AgendaSurat;
use Illuminate\Support\Collection;

interface AgendaSuratRepositoryInterface
{
    public function store(AgendaSuratData $data): AgendaSurat;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): AgendaSurat;

    public function update(AgendaSurat $agendaSurat, AgendaSuratData $data): AgendaSurat;

    public function delete(AgendaSurat $agendaSurat): void;
}
