<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Services;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaTimPenggerakScopeService
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

    public function canView(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        if (! $this->canAccessLevel($user, $anggotaTimPenggerak->level)) {
            return false;
        }

        return (int) $anggotaTimPenggerak->area_id === (int) $user->area_id;
    }

    public function canUpdate(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        return $this->canView($user, $anggotaTimPenggerak);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAndArea(AnggotaTimPenggerak $anggotaTimPenggerak, string $level, int $areaId): AnggotaTimPenggerak
    {
        if ($anggotaTimPenggerak->level !== $level || (int) $anggotaTimPenggerak->area_id !== $areaId) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaTimPenggerak;
    }
}

