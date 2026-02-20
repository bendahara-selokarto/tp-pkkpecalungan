<?php

namespace App\Domains\Wilayah\Repositories;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Support\Collection;

interface AreaRepositoryInterface
{
    public function find(int $id): Area;

    public function getLevelById(int $id): ?string;

    public function getDesaByKecamatan(int $kecamatanId): Collection;

    public function getByUser(User $user): Collection;
}
