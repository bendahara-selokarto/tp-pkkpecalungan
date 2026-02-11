<?php

namespace App\Domains\Wilayah\Repositories;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;

class AreaRepository implements AreaRepositoryInterface
{
    public function getByUser(User $user)
    {
        if ($user->isKecamatan()) {
            return Area::where('level','desa')
                ->where('parent_id', $user->area_id)
                ->get();
        }

        return Area::where('id', $user->area_id)->get();
    }

    public function getDesaByKecamatan(int $kecamatanId)
    {
        return Area::where('level','desa')
            ->where('parent_id', $kecamatanId)
            ->get();
    }

    public function find(int $id)
    {
        return Area::findOrFail($id);
    }
}
