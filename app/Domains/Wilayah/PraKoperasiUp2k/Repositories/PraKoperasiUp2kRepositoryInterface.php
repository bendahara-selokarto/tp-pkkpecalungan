<?php

namespace App\Domains\Wilayah\PraKoperasiUp2k\Repositories;

use App\Domains\Wilayah\PraKoperasiUp2k\DTOs\PraKoperasiUp2kData;
use App\Domains\Wilayah\PraKoperasiUp2k\Models\PraKoperasiUp2k;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PraKoperasiUp2kRepositoryInterface
{
    public function store(PraKoperasiUp2kData $data): PraKoperasiUp2k;

    public function paginateByLevelAndArea(string $level, int $areaId, int $tahunAnggaran, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, int $tahunAnggaran): Collection;

    public function find(int $id): PraKoperasiUp2k;

    public function update(PraKoperasiUp2k $praKoperasiUp2k, PraKoperasiUp2kData $data): PraKoperasiUp2k;

    public function delete(PraKoperasiUp2k $praKoperasiUp2k): void;
}
