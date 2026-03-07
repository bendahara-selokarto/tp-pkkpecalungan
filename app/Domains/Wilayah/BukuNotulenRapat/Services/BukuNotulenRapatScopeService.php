<?php

namespace App\Domains\Wilayah\BukuNotulenRapat\Services;

use App\Domains\Wilayah\BukuNotulenRapat\Models\BukuNotulenRapat;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuNotulenRapatScopeService
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

    public function canView(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        if (! $this->canAccessLevel($user, $bukuNotulenRapat->level)) {
            return false;
        }

        return (int) $bukuNotulenRapat->area_id === (int) $user->area_id
            && (int) $bukuNotulenRapat->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BukuNotulenRapat $bukuNotulenRapat): bool
    {
        return $this->canView($user, $bukuNotulenRapat);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuNotulenRapat $bukuNotulenRapat, string $level, int $areaId, int $tahunAnggaran): BukuNotulenRapat
    {
        if (
            $bukuNotulenRapat->level !== $level
            || (int) $bukuNotulenRapat->area_id !== $areaId
            || (int) $bukuNotulenRapat->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuNotulenRapat;
    }
    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
