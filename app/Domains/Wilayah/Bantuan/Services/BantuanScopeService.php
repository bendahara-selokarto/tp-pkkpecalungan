<?php

namespace App\Domains\Wilayah\Bantuan\Services;

use App\Domains\Wilayah\Bantuan\Models\Bantuan;
use App\Domains\Wilayah\Repositories\AreaRepositoryInterface;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BantuanScopeService
{
    public function __construct(
        private readonly AreaRepositoryInterface $areaRepository
    ) {
    }

    public function canAccessLevel(User $user, string $level): bool
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if ($level === 'desa') {
            return $user->hasRoleForScope('desa') && $areaLevel === 'desa';
        }

        if ($level === 'kecamatan') {
            return $user->hasRoleForScope('kecamatan') && $areaLevel === 'kecamatan';
        }

        return false;
    }

    public function canEnterModule(User $user): bool
    {
        $areaLevel = $this->resolveUserAreaLevel($user);

        if (! is_string($areaLevel)) {
            return false;
        }

        return $this->canAccessLevel($user, $areaLevel);
    }

    public function canView(User $user, Bantuan $bantuan): bool
    {
        if (! $this->canAccessLevel($user, $bantuan->level)) {
            return false;
        }

        return (int) $bantuan->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Bantuan $bantuan): bool
    {
        return $this->canView($user, $bantuan);
    }

    public function requireUserAreaId(): int
    {
        $areaId = auth()->user()?->area_id;

        if (! is_numeric($areaId)) {
            throw new HttpException(403, 'Area pengguna belum ditentukan.');
        }

        return (int) $areaId;
    }

    public function authorizeSameLevelAndArea(Bantuan $bantuan, string $level, int $areaId): Bantuan
    {
        if ($bantuan->level !== $level || (int) $bantuan->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bantuan;
    }

    private function resolveUserAreaLevel(User $user): ?string
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
}
