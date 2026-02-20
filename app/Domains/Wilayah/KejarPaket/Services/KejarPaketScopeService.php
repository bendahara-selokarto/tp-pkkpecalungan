<?php

namespace App\Domains\Wilayah\KejarPaket\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\KejarPaket\Models\KejarPaket;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KejarPaketScopeService
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

    public function canView(User $user, KejarPaket $kejarPaket): bool
    {
        if (! $this->canAccessLevel($user, $kejarPaket->level)) {
            return false;
        }

        return (int) $kejarPaket->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, KejarPaket $kejarPaket): bool
    {
        return $this->canView($user, $kejarPaket);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(KejarPaket $kejarPaket, string $level, int $areaId): KejarPaket
    {
        if ($kejarPaket->level !== $level || (int) $kejarPaket->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $kejarPaket;
    }
}





