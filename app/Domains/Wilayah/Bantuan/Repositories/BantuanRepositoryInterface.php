<?php

namespace App\Domains\Wilayah\Bantuan\Repositories;

use App\Domains\Wilayah\Bantuan\DTOs\BantuanData;
use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use Illuminate\Support\Collection;

interface BantuanRepositoryInterface
{
    public function store(BantuanData $data): Bantuan;

    public function getByLevelAndArea(string $level, int $areaId): Collection;

    public function find(int $id): Bantuan;

    public function update(Bantuan $bantuan, BantuanData $data): Bantuan;

    public function delete(Bantuan $bantuan): void;
}


