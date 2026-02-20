<?php

namespace App\Domains\Wilayah\Bkr\Repositories;

use App\Domains\Wilayah\Bkr\DTOs\BkrData;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use Illuminate\Support\Collection;

interface BkrRepositoryInterface
{
    public function store(BkrData $data): Bkr;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Bkr;

    public function update(Bkr $bkr, BkrData $data): Bkr;

    public function delete(Bkr $bkr): void;
}


