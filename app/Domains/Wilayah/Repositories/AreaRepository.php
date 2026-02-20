<?php

namespace App\Domains\Wilayah\Repositories;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Support\Collection;

class AreaRepository implements AreaRepositoryInterface
{
    public function find(int $id): Area
    {
        return Area::findOrFail($id);
    }

    public function getLevelById(int $id): ?string
    {
        return Area::query()
            ->whereKey($id)
            ->value('level');
    }

    public function getDesaByKecamatan(int $kecamatanId): Collection
    {
        return Area::where('parent_id', $kecamatanId)
            ->where('level', 'desa')
            ->get();
    }

    public function getByUser(User $user): Collection
    {
        if ($user->scope === 'kecamatan') {
            $kecamatan = Area::findOrFail($user->area_id);

            return Area::where('parent_id', $kecamatan->id)
                ->where('level', 'desa')
                ->get();
        }

        if ($user->scope === 'desa') {
            return Area::where('id', $user->area_id)
                ->where('level', 'desa')
                ->get();
        }

        return collect();
    }
}
