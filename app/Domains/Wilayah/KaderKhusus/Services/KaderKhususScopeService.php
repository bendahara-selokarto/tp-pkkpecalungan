<?php

namespace App\Domains\Wilayah\KaderKhusus\Services;

use App\Domains\Wilayah\KaderKhusus\Models\KaderKhusus;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class KaderKhususScopeService
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

    public function canView(User $user, KaderKhusus $kaderKhusus): bool
    {
        if (! $this->canAccessLevel($user, $kaderKhusus->level)) {
            return false;
        }

        return (int) $kaderKhusus->area_id === (int) $user->area_id
            && (int) $kaderKhusus->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, KaderKhusus $kaderKhusus): bool
    {
        return $this->canView($user, $kaderKhusus);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(KaderKhusus $kaderKhusus, string $level, int $areaId, int $tahunAnggaran): KaderKhusus
    {
        if (
            $kaderKhusus->level !== $level
            || (int) $kaderKhusus->area_id !== $areaId
            || (int) $kaderKhusus->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $kaderKhusus;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
