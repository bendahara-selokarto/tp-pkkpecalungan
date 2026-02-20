<?php

namespace App\Domains\Wilayah\Bkr\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BkrScopeService
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

    public function canView(User $user, Bkr $bkr): bool
    {
        if (! $this->canAccessLevel($user, $bkr->level)) {
            return false;
        }

        return (int) $bkr->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, Bkr $bkr): bool
    {
        return $this->canView($user, $bkr);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(Bkr $bkr, string $level, int $areaId): Bkr
    {
        if ($bkr->level !== $level || (int) $bkr->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bkr;
    }
}


