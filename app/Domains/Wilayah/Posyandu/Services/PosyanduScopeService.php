<?php

namespace App\Domains\Wilayah\Posyandu\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PosyanduScopeService
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

    public function canView(User $user, Posyandu $posyandu): bool
    {
        if (! $this->canAccessLevel($user, $posyandu->level)) {
            return false;
        }

        return (int) $posyandu->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Posyandu $posyandu): bool
    {
        return $this->canView($user, $posyandu);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(Posyandu $posyandu, string $level, int $areaId): Posyandu
    {
        if ($posyandu->level !== $level || (int) $posyandu->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $posyandu;
    }
}





