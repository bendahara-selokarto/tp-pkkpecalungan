<?php

namespace App\Domains\Wilayah\Repositories;

use App\Domains\Wilayah\Enums\ScopeLevel;
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
            ->where('level', ScopeLevel::DESA->value)
            ->get();
    }

    public function getByUser(User $user): Collection
    {
        if (! is_numeric($user->area_id)) {
            return collect();
        }

        $areaId = (int) $user->area_id;
        $areaLevel = $this->getLevelById($areaId);

        if (
            $user->hasRoleForScope(ScopeLevel::KECAMATAN->value)
            && $areaLevel === ScopeLevel::KECAMATAN->value
        ) {
            return Area::where('parent_id', $areaId)
                ->where('level', ScopeLevel::DESA->value)
                ->get();
        }

        if (
            $user->hasRoleForScope(ScopeLevel::DESA->value)
            && $areaLevel === ScopeLevel::DESA->value
        ) {
            return Area::where('id', $areaId)
                ->where('level', ScopeLevel::DESA->value)
                ->get();
        }

        return collect();
    }
}
