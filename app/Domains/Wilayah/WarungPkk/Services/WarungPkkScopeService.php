<?php

namespace App\Domains\Wilayah\WarungPkk\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\WarungPkk\Models\WarungPkk;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WarungPkkScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
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

        return (int) $warungPkk->area_id === (int) $user->area_id
            && (int) $warungPkk->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, WarungPkk $warungPkk): bool
    {
        return $this->canView($user, $warungPkk);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(WarungPkk $warungPkk, string $level, int $areaId, int $tahunAnggaran): WarungPkk
    {
        if (
            $warungPkk->level !== $level
            || (int) $warungPkk->area_id !== $areaId
            || (int) $warungPkk->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $warungPkk;
    }
}
