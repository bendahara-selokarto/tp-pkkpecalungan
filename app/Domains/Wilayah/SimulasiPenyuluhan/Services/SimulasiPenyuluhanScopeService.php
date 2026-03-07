<?php

namespace App\Domains\Wilayah\SimulasiPenyuluhan\Services;

use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Domains\Wilayah\SimulasiPenyuluhan\Models\SimulasiPenyuluhan;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SimulasiPenyuluhanScopeService
{
    public function __construct(
        private readonly UserAreaContextService $userAreaContextService,
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService
    ) {}

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
    }

    public function canEnterModule(User $user): bool
    {
        return $this->userAreaContextService->canEnterModule($user);
    }

    public function canView(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        if (! $this->canAccessLevel($user, $simulasiPenyuluhan->level)) {
            return false;
        }

        return (int) $simulasiPenyuluhan->area_id === (int) $user->area_id
            && (int) $simulasiPenyuluhan->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, SimulasiPenyuluhan $simulasiPenyuluhan): bool
    {
        return $this->canView($user, $simulasiPenyuluhan);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(SimulasiPenyuluhan $simulasiPenyuluhan, string $level, int $areaId, int $tahunAnggaran): SimulasiPenyuluhan
    {
        if (
            $simulasiPenyuluhan->level !== $level
            || (int) $simulasiPenyuluhan->area_id !== $areaId
            || (int) $simulasiPenyuluhan->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $simulasiPenyuluhan;
    }
}
