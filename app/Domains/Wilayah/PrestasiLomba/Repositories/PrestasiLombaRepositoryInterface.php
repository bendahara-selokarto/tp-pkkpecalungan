<?php

namespace App\Domains\Wilayah\PrestasiLomba\Repositories;

use App\Domains\Wilayah\PrestasiLomba\DTOs\PrestasiLombaData;
use App\Domains\Wilayah\PrestasiLomba\Models\PrestasiLomba;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PrestasiLombaRepositoryInterface
{
    public function store(PrestasiLombaData $data): PrestasiLomba;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage, ?int $creatorIdFilter = null): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId, ?int $creatorIdFilter = null): Collection;

    public function find(int $id): PrestasiLomba;

    public function update(PrestasiLomba $prestasiLomba, PrestasiLombaData $data): PrestasiLomba;

    public function delete(PrestasiLomba $prestasiLomba): void;
}

