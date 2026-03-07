<?php

namespace App\Domains\Wilayah\Bkr\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Bkr\Models\Bkr;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BkrScopeService
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

    public function canView(User $user, Bkr $bkr): bool
    {
        if (! $this->canAccessLevel($user, $bkr->level)) {
            return false;
        }

        return (int) $bkr->area_id === (int) $user->area_id
            && (int) $bkr->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Bkr $bkr): bool
    {
        return $this->canView($user, $bkr);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Bkr $bkr, string $level, int $areaId, int $tahunAnggaran): Bkr
    {
        if (
            $bkr->level !== $level
            || (int) $bkr->area_id !== $areaId
            || (int) $bkr->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bkr;
    }
}

