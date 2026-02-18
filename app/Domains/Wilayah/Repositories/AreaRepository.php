<?php

namespace App\Domains\Wilayah\Repositories;

use App\Models\User;
use App\Domains\Wilayah\Models\Area;
use Illuminate\Support\Collection;

class AreaRepository implements AreaRepositoryInterface
{
    public function find(int $id): Area
    {
        return Area::findOrFail($id);
    }

    public function getDesaByKecamatan(int $kecamatanId): Collection
    {
        return Area::where('parent_id', $kecamatanId)
            ->where('level', 'desa')
            ->get();
    }

    /**
     * 🔥 Tambahkan ini
     */
    public function getByUser(User $user): Collection
    {
        // Jika user level kecamatan
        if ($user->scope === 'kecamatan') {

            $kecamatan = Area::findOrFail($user->area_id);

            return Area::where('parent_id', $kecamatan->id)
                ->where('level', 'desa')
                ->get();
        }

        // Jika user level desa
        if ($user->scope === 'desa') {

            return Area::where('id', $user->area_id)
                ->where('level', 'desa')
                ->get();
        }

        return collect();
    }
}
