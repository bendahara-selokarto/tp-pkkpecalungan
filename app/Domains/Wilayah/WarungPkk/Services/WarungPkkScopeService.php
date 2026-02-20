<?php

namespace App\Domains\Wilayah\WarungPkk\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WarungPkkScopeService
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

    public function canView(User $user, WarungPkk $warungPkk): bool
    {
        if (! $this->canAccessLevel($user, $warungPkk->level)) {
            return false;
        }

        return (int) $warungPkk->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, WarungPkk $warungPkk): bool
    {
        return $this->canView($user, $warungPkk);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(WarungPkk $warungPkk, string $level, int $areaId): WarungPkk
    {
        if ($warungPkk->level !== $level || (int) $warungPkk->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $warungPkk;
    }
}
