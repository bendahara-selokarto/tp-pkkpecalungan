<?php

namespace App\Domains\Wilayah\Posyandu\Services;

use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\Posyandu\Models\Posyandu;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PosyanduScopeService
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

    public function canView(User $user, Posyandu $posyandu): bool
    {
        if (! $this->canAccessLevel($user, $posyandu->level)) {
            return false;
        }

        return (int) $posyandu->area_id === (int) $user->area_id
            && (int) $posyandu->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Posyandu $posyandu): bool
    {
        return $this->canView($user, $posyandu);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Posyandu $posyandu, string $level, int $areaId, int $tahunAnggaran): Posyandu
    {
        if (
            $posyandu->level !== $level
            || (int) $posyandu->area_id !== $areaId
            || (int) $posyandu->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $posyandu;
    }
}




