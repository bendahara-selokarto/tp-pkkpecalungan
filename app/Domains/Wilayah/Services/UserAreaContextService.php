<?php

namespace App\Domains\Wilayah\Services;

use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserAreaContextService
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function canAccessLevel(User $user, string $level): bool
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if ($level === ScopeLevel::DESA->value) {
            return $user->hasRoleForScope(ScopeLevel::DESA->value) && $areaLevel === ScopeLevel::DESA->value;
        }

        if ($level === ScopeLevel::KECAMATAN->value) {
            return $user->hasRoleForScope(ScopeLevel::KECAMATAN->value) && $areaLevel === ScopeLevel::KECAMATAN->value;
        }

        return false;
    }

    public function canEnterModule(User $user): bool
    {
        return is_string($this->resolveEffectiveScope($user));
    }

    public function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }

    public function resolveUserAreaLevel(User $user): ?string
    {
        if (! is_numeric($user->area_id)) {
            return null;
        }

        $loadedLevel = $user->relationLoaded('area') ? $user->area?->level : null;
        if (is_string($loadedLevel)) {
            return $loadedLevel;
        }

        return $this->areaRepository->getLevelById((int) $user->area_id);
    }

    public function resolveEffectiveScope(User $user): ?string
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if (! is_string($areaLevel)) {
            return null;
        }

        return $this->canAccessLevel($user, $areaLevel) ? $areaLevel : null;
    }
}
