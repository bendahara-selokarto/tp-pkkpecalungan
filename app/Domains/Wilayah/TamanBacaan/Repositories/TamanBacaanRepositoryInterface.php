<?php

namespace App\Domains\Wilayah\TamanBacaan\Repositories;

use App\Domains\Wilayah\TamanBacaan\DTOs\TamanBacaanData;
use App\Domains\Wilayah\TamanBacaan\Models\TamanBacaan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TamanBacaanRepositoryInterface
{
    public function store(TamanBacaanData $data): TamanBacaan;

    public function paginateByLevelAndArea(string $level, int $areaId, int $perPage): LengthAwarePaginator;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): TamanBacaan;

    public function update(TamanBacaan $tamanBacaan, TamanBacaanData $data): TamanBacaan;

    public function delete(TamanBacaan $tamanBacaan): void;
}

