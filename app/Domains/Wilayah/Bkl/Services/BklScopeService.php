<?php

namespace App\Domains\Wilayah\Bkl\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Bkl\Models\Bkl;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BklScopeService
{
    public function __construct(
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
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

    public function canView(User $user, Bkl $bkl): bool
    {
        if (! $this->canAccessLevel($user, $bkl->level)) {
            return false;
        }

        return (int) $bkl->area_id === (int) $user->area_id
            && (int) $bkl->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Bkl $bkl): bool
    {
        return $this->canView($user, $bkl);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Bkl $bkl, string $level, int $areaId, int $tahunAnggaran): Bkl
    {
        if (
            $bkl->level !== $level
            || (int) $bkl->area_id !== $areaId
            || (int) $bkl->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bkl;
    }
}
