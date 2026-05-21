<?php

namespace App\Domains\Wilayah\BukuEkspedisi\Services;

use App\Domains\Wilayah\BukuEkspedisi\Models\BukuEkspedisi;
use App\Domains\Wilayah\Services\ActiveBudgetYearContextService;
use App\Domains\Wilayah\Services\UserAreaContextService;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuEkspedisiScopeService
{
    public function __construct(
        private readonly ActiveBudgetYearContextService $activeBudgetYearContextService,
        private readonly UserAreaContextService $userAreaContextService
    ) {}

    public function canAccessLevel(User $user, string $level): bool
    {
        return $this->userAreaContextService->canAccessLevel($user, $level);
    }

    public function canEnterModule(User $user): bool
    {
        return $this->userAreaContextService->canEnterModule($user);
    }

    public function canView(User $user, BukuEkspedisi $bukuEkspedisi): bool
    {
        if (! $this->canAccessLevel($user, $bukuEkspedisi->level)) {
            return false;
        }

        return (int) $bukuEkspedisi->area_id === (int) $user->area_id
            && (int) $bukuEkspedisi->tahun_anggaran === $this->activeBudgetYearContextService->resolveForUser($user);
    }

    public function canUpdate(User $user, BukuEkspedisi $bukuEkspedisi): bool
    {
        return $this->canView($user, $bukuEkspedisi);
    }

    public function requireUserAreaId(): int
    {
        return $this->userAreaContextService->requireUserAreaId();
    }

    public function authorizeSameLevelAreaAndBudgetYear(BukuEkspedisi $bukuEkspedisi, string $level, int $areaId, int $tahunAnggaran): BukuEkspedisi
    {
        if (
            $bukuEkspedisi->level !== $level
            || (int) $bukuEkspedisi->area_id !== $areaId
            || (int) $bukuEkspedisi->tahun_anggaran !== $tahunAnggaran
        ) {
            throw new HttpException(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return $bukuEkspedisi;
    }

    public function requireActiveBudgetYear(): int
    {
        return $this->activeBudgetYearContextService->requireForAuthenticatedUser();
    }

    public function resolveCreatorIdFilterForList(string $level): ?int
    {
        return $this->userAreaContextService->resolveCreatorIdFilterForKecamatanSekretaris($level);
    }
}
