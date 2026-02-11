<?php

namespace App\Domains\Wilayah\Repositories;

use App\Models\User;

interface AreaRepositoryInterface
{
    public function getByUser(User $user);

    public function getDesaByKecamatan(int $kecamatanId);

    public function find(int $id);
}
