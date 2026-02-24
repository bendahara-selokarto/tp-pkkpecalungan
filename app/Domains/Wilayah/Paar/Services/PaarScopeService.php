<?php

namespace App\Domains\Wilayah\Paar\Services;

use App\Domains\Wilayah\Paar\Models\Paar;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaarScopeService
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

    public function canView(User $user, Paar $paar): bool
    {
        if (! $this->canAccessLevel($user, $paar->level)) {
            return false;
        }

        return (int) $paar->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Paar $paar): bool
    {
        return $this->canView($user, $paar);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(Paar $paar, string $level, int $areaId): Paar
    {
        if ($paar->level !== $level || (int) $paar->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $paar;
    }
}