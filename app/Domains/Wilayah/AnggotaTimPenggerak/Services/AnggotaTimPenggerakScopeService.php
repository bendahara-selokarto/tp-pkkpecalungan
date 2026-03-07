<?php

namespace App\Domains\Wilayah\AnggotaTimPenggerak\Services;

use App\Domains\Wilayah\AnggotaTimPenggerak\Models\AnggotaTimPenggerak;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaTimPenggerakScopeService
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

    public function canView(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        if (! $this->canAccessLevel($user, $anggotaTimPenggerak->level)) {
            return false;
        }

        return (int) $anggotaTimPenggerak->area_id === (int) $user->area_id
            && (int) $anggotaTimPenggerak->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, AnggotaTimPenggerak $anggotaTimPenggerak): bool
    {
        return $this->canView($user, $anggotaTimPenggerak);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(AnggotaTimPenggerak $anggotaTimPenggerak, string $level, int $areaId, int $tahunAnggaran): AnggotaTimPenggerak
    {
        if (
            $anggotaTimPenggerak->level !== $level
            || (int) $anggotaTimPenggerak->area_id !== $areaId
            || (int) $anggotaTimPenggerak->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $anggotaTimPenggerak;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
