<?php

namespace App\Domains\Wilayah\LiterasiWarga\Repositories;

use App\Domains\Wilayah\LiterasiWarga\DTOs\LiterasiWargaData;
use App\Domains\Wilayah\LiterasiWarga\Models\LiterasiWarga;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LiterasiWargaRepositoryInterface
{
    public function store(LiterasiWargaData $data): LiterasiWarga;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function find(int $id): LiterasiWarga;

    public function update(LiterasiWarga $literasiWarga, LiterasiWargaData $data): LiterasiWarga;

    public function delete(LiterasiWarga $literasiWarga): void;
}
