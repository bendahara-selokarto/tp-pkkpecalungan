<?php

namespace Database\Seeders;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;

class SyncUserScopeAreaSeeder extends Seeder
{
    public function run(): void
    {
        $defaultKecamatan = Area::where('level', 'kecamatan')->orderBy('id')->first();
        $defaultDesa = Area::where('level', 'desa')->orderBy('id')->first();

        User::with('roles')->chunkById(100, function ($users) use ($defaultKecamatan, $defaultDesa) {
            foreach ($users as $user) {
                $targetScope = $this->resolveScopeFromRole($user);

                if ($targetScope === null) {
                    continue;
                }

                $targetAreaId = $this->resolveAreaId($user, $targetScope, $defaultKecamatan?->id, $defaultDesa?->id);

                if ($targetAreaId === null) {
                    continue;
                }

                $user->forceFill([
                    'scope' => $targetScope,
                    'area_id' => $targetAreaId,
                ])->save();
            }
        });
    }

    private function resolveScopeFromRole(User $user): ?string
    {
        if ($user->hasRole('admin-kecamatan') || $user->hasRole('super-admin')) {
            return 'kecamatan';
        }

        if ($user->hasRole('admin-desa')) {
            return 'desa';
        }

        return null;
    }

    private function resolveAreaId(
        User $user,
        string $targetScope,
        ?int $defaultKecamatanId,
        ?int $defaultDesaId
    ): ?int {
        $currentArea = is_numeric($user->area_id)
            ? Area::find((int) $user->area_id)
            : null;

        if ($targetScope === 'kecamatan') {
            if ($currentArea?->level === 'kecamatan') {
                return $currentArea->id;
            }

            if ($currentArea?->level === 'desa' && is_numeric($currentArea->parent_id)) {
                return (int) $currentArea->parent_id;
            }

            return $defaultKecamatanId;
        }

        if ($currentArea?->level === 'desa') {
            return $currentArea->id;
        }

        if ($currentArea?->level === 'kecamatan') {
            $desaInKecamatan = Area::where('level', 'desa')
                ->where('parent_id', $currentArea->id)
                ->orderBy('id')
                ->value('id');

            if (is_numeric($desaInKecamatan)) {
                return (int) $desaInKecamatan;
            }
        }

        return $defaultDesaId;
    }
}
