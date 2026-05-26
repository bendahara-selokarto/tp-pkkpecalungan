<?php

namespace App\Domains\Wilayah\Kliping\Services;

use App\Domains\Wilayah\Kliping\Models\Kliping;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KlipingScopeService
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

    public function canView(User $user, Kliping $kliping): bool
    {
        if (! $this->canAccessLevel($user, $kliping->level)) {
            return false;
        }

        return (int) $kliping->area_id === (int) $user->area_id
            && (int) $kliping->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, Kliping $kliping): bool
    {
        return $this->canView($user, $kliping);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(Kliping $kliping, string $level, int $areaId, int $tahunAnggaran): Kliping
    {
        if (
            $kliping->level !== $level
            || (int) $kliping->area_id !== $areaId
            || (int) $kliping->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $kliping;
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
