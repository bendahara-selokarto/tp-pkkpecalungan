<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SimulasiPenyuluhanScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService
    ) {
    }

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
    }

    public function canEnterModule(User $user): bool
    {
        return $this->userAreaContextService->canEnterModule($user);
    }

    public function canView(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        if (! $this->canAccessLevel($user, $simulasiPenyuluhan->level)) {
            return false;
        }

        return (int) $simulasiPenyuluhan->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        return $this->canView($user, $simulasiPenyuluhan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(SimulasiPenyuluhan $simulasiPenyuluhan, string $level, int $areaId): SimulasiPenyuluhan
    {
        if ($simulasiPenyuluhan->level !== $level || (int) $simulasiPenyuluhan->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $simulasiPenyuluhan;
    }
}
